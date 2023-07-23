<?php

namespace App\Http\Normalizer;

use App\Http\Config\RelationConfiguration;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

abstract class AbstractNormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    protected PropertyAccessorInterface $propertyAccessor;

    public function __construct(protected readonly EntityManagerInterface $entityManager)
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableExceptionOnInvalidPropertyPath()
            ->disableExceptionOnInvalidIndex()
            ->getPropertyAccessor();
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        return array_key_exists('@id', $data)
            ? $this->doDenormalize($data, $type, $format, $context)
            : $this->denormalizer->denormalize($data, $type.'[]', 'json');
    }

    abstract public function supportsDenormalization(mixed $data, string $type, string $format = null);

    abstract public function doDenormalize(mixed $data, string $type, string $format = null, array $context = []);

    protected function hydrateRoot($object, array $data = [])
    {
        foreach ($data as $property => $value) {
            $this->propertyAccessor->setValue($object, $property, $value)
            ;
        }
        $object->setId($data['@id']);

        return $object;
    }

    protected function hydrateDatabaseProperties(array $databaseFields, string $entityClass, $object): void
    {
        if ([] === $databaseFields) {
            return;
        }

        $alias = 'entity';
        \array_walk(
            $databaseFields,
            function (&$a, $key, $alias) {
                $a = $alias.'.'.$a;
            },
            $alias
        );

        $result = $this->entityManager->createQueryBuilder()
            ->from($entityClass, $alias)
            ->select($databaseFields)
            ->where($alias.'.id = :id')
            ->setParameter('id', $object->getId())
        ->getQuery()
        ->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

        foreach ($result as $field => $value) {
            /* @var PropertyAccessorInterface */
            $this->propertyAccessor->setValue($object, $field, $value);
        }
    }

    /**
     * @param RelationConfiguration[] $relationConfigurations
     */
    protected function hydrateDatabaseRelations(array $relationConfigurations, $object): void
    {
        $result = [];
        foreach ($relationConfigurations as $relationConfiguration) {
            $result =
                $this->entityManager
                    ->getRepository($relationConfiguration->getFqcn())
                    ->findBy([$relationConfiguration->getIdentifier() => $object->getId()])
            ;
        }
        if ([] !== $result) {
            $this->propertyAccessor->setValue($object, $relationConfiguration->getPropertyPath(), $result);
        }
    }
}

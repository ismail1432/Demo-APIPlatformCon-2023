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

    abstract public function denormalize(mixed $data, string $type, string $format = null, array $context = []);

    abstract public function supportsDenormalization(mixed $data, string $type, string $format = null);

    protected function hydrateRoot($object, array $data = [])
    {
        foreach ($data as $property => $value) {
            $method = 'set'.\ucfirst($property);
            if (\method_exists($object, $method)) {
                $object->{$method}($value);
            }
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
        $qb = $this->entityManager->createQueryBuilder()
            ->from($entityClass, $alias)
            ->select($databaseFields)
            ->where($alias.'.id = :id')
            ->setParameter('id', $object->getId());

        $result = $qb->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

        foreach ($result as $field => $value) {
            $this->propertyAccessor->setValue($object, $field, $value);
        }
    }

    /**
     * @param RelationConfiguration[] $oneToManyConfigurations
     */
    protected function hydrateDatabaseRelations(array $oneToManyConfigurations, $object): void
    {
        foreach ($oneToManyConfigurations as $oneToManyConfiguration) {
            $result =
                $this->entityManager
                    ->getRepository($oneToManyConfiguration->getFqcn())
                    ->findBy([$oneToManyConfiguration->getIdentifier() => $object->getId()])
            ;
        }
        if ([] !== $result) {
            $this->propertyAccessor->setValue($object, $oneToManyConfiguration->getPropertyPath(), $result);
        }
    }
}

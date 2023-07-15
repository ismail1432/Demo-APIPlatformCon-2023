<?php

namespace App\Http\Normalizer;

use App\Entity\Adresse;
use App\Entity\Utilisateur;
use App\Http\Config\OneToManyConfiguration;
use App\Http\Config\UserConfiguration;
use App\Http\Model\User;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class UserNormalizer implements DenormalizerInterface
{
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
        /** @var User $user */
        $user = $this->hydrateRoot(new User(), $data);

        if (null !== $dataAddress = $data['address'] ?? null) {
            $adresse = Adresse::create(
                $dataAddress['street_name'] ?? '',
                $dataAddress['city'] ?? '',
                $dataAddress['zip_code'] ?? '',
            );
            $user->setAdresse($adresse);
        }

        $userConfiguration = new UserConfiguration();

        $this->hydrateDatabaseProperties($userConfiguration->getFields(), Utilisateur::class, $user);

        $this->hydrateOneToManyRelations($userConfiguration->getOneToManyConfiguration(), $user);

        return $user;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null)
    {
        return User::class === $type;
    }

    public function hydrateRoot($object, array $data = [])
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
     * @param OneToManyConfiguration[] $oneToManyConfigurations
     */
    protected function hydrateOneToManyRelations(array $oneToManyConfigurations, $object): void
    {
        foreach ($oneToManyConfigurations as $oneToManyConfiguration) {
            $result = $this->entityManager->getRepository($oneToManyConfiguration->getFqcn())
                ->findBy([$oneToManyConfiguration->getIdentifier() => $object->getId()]);

            if (!empty($result)) {
                $this->propertyAccessor->setValue($object, $oneToManyConfiguration->getPropertyPath(), $result);
            }
        }
    }
}

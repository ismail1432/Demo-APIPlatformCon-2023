<?php

namespace App\Doctrine\Listener;

use App\Http\Config\ContainsApiResource;
use App\Http\HttpRepositoryRegistry;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class EntityWithMigratedRelationPostLoadSubscriber implements EventSubscriberInterface
{
    private PropertyAccessorInterface $propertyAccessor;
    private array $fetched = [];

    public function __construct(private readonly HttpRepositoryRegistry $httpRepositoryRegistry)
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableExceptionOnInvalidPropertyPath()
            ->disableExceptionOnInvalidIndex()
            ->getPropertyAccessor();
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postLoad,
        ];
    }

    public function postLoad(PostLoadEventArgs $loadEventArgs)
    {
        if ($this->supports($loadEventArgs)) {
            /** @var $object ContainsApiResource */
            $object = $loadEventArgs->getObject();
            foreach ($object->getRelationsConfiguration() as $relationConfiguration) {
                $value = $this->propertyAccessor->getValue($object, $relationConfiguration->getPropertyPath());
                $identifier = $this->propertyAccessor->getValue($value, $relationConfiguration->getIdentifier());

                $fetched = $this->fetched[$relationConfiguration->getFqcn().$identifier] ?? null;
                if (null === $fetched) {
                    $fetched = $this->httpRepositoryRegistry->getRepository($relationConfiguration->getFqcn())->find($identifier);
                }
                $this->propertyAccessor->setValue($object, $relationConfiguration->getPropertyPath(), $fetched);
                $this->fetched[] = $relationConfiguration->getFqcn().$identifier;
            }
        }
    }

    public function supports(PostLoadEventArgs $loadEventArgs)
    {
        return $loadEventArgs->getObject() instanceof ContainsApiResource;
    }
}

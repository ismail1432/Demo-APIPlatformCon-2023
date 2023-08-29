<?php

namespace App\Doctrine\Listener;

use App\Http\Config\ContainsApiResource;
use App\Http\Repository\HttpRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Events;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class EntityWithMigratedRelationPostLoadSubscriber implements EventSubscriberInterface
{
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(
        #[TaggedLocator('app.http_repository', defaultIndexMethod: 'getModelClassName')]
        private ContainerInterface $httpRepositoryLocator
    ) {
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
        if ($loadEventArgs->getObject() instanceof ContainsApiResource) {
            /** @var $object ContainsApiResource */
            $object = $loadEventArgs->getObject();
            foreach ($object->getRelationsConfiguration() as $relationConfiguration) {
                if (!$this->httpRepositoryLocator->has($relationConfiguration->getFqcn())) {
                    throw new \LogicException(sprintf('No http repository found for "%s"', $relationConfiguration->getFqcn()));
                }

                /** @var HttpRepositoryInterface $httpRepository */
                $httpRepository = $this->httpRepositoryLocator->get($relationConfiguration->getFqcn());
                $property = $this->propertyAccessor->getValue($object, $relationConfiguration->getPropertyPath());
                $idValue = $this->propertyAccessor->getValue($property, $relationConfiguration->getIdentifier());

                $fetched = $httpRepository->find($idValue);
                $this->propertyAccessor->setValue($object, $relationConfiguration->getPropertyPath(), $fetched);
            }
        }
    }
}

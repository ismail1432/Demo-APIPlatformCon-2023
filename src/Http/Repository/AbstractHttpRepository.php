<?php

namespace App\Http\Repository;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractHttpRepository implements HttpRepositoryInterface
{
    private ?AdapterInterface $cache;

    public function __construct(protected readonly HttpClientInterface $apiClient, protected readonly SerializerInterface $serializer)
    {
        $this->cache = new FilesystemAdapter();
    }

    abstract public static function getModelClassName(): string;

    public function find(string $id, array $context = [])
    {
        return $this->doFind(sprintf('/%s/%s', $this->getResourcesUri(), $id), $context);
    }

    public function findAll()
    {
        return $this->doFind(sprintf('/%s', $this->getResourcesUri()));
    }

    private function doFind(string $uri, array $context = [])
    {
        $cacheKey = \str_replace('/', '', $uri);

        $item = $this->cache->getItem($cacheKey);

        if ($item->isHit()) {
            return $item->get();
        }

        $response = $this->apiClient->request('GET', $uri);

        $object = $this->serializer->deserialize(
            $response->getContent(),
            static::getModelClassName(),
            'json',
            $context
        );

        $item->set($object);
        $this->cache->save($item);

        return $object;
    }
}

<?php

namespace App\Http\Repository;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractHttpRepository
{
    public function __construct(protected readonly HttpClientInterface $apiClient, protected readonly SerializerInterface $serializer)
    {
    }

    abstract public function getResourcesUri(): string;

    abstract public function getEntityClassName(): string;

    public function find(string $id)
    {
        $response = $this->apiClient->request('GET', sprintf('/%s/%s', $this->getResourcesUri(), $id));

        return $this->serializer->deserialize(
            $response->getContent(),
            $this->getEntityClassName(),
            'json'
        );
    }
}

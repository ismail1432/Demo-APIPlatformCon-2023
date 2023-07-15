<?php

namespace App\Http;

use App\Http\Repository\HttpRepositoryInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class HttpRepositoryRegistry
{
    /** @var HttpRepositoryInterface[] */
    private array $httpRepository = [];

    /**
     * @param HttpRepositoryInterface[] $httpRepository
     */
    public function __construct(
        #[TaggedIterator('app.http_repository')] iterable $httpRepository
    ) {
        foreach ($httpRepository as $httpRepo) {
            $this->httpRepository[$httpRepo::getEntityClassName()] = $httpRepo;
        }
    }

    public function getRepository(string $fqcn): HttpRepositoryInterface
    {
        $repo = $this->httpRepository[$fqcn] ?? null;

        if (null === $repo) {
            throw new \RuntimeException('No http repository found for class "%s"', $fqcn);
        }

        return $repo;
    }
}

<?php

namespace App\Http\Repository;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(tags: ['app.http_repository'])]
interface HttpRepositoryInterface
{
    public function find(string $id, array $context);

    public function getResourcesUri(): string;

    public static function getModelClassName(): string;
}

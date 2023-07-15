<?php

namespace App\Http\Repository;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(tags: ['app.http_repository'])]
interface HttpRepositoryInterface
{
    public static function getEntityClassName(): string;

    public function find(string $id);

    public function getResourcesUri(): string;

    public function getModelClassName(): string;
}

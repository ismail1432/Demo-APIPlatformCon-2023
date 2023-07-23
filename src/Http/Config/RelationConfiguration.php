<?php

namespace App\Http\Config;

final class RelationConfiguration
{
    public function __construct(
        private readonly string $fqcn,
        private readonly string $identifier,
        private readonly string $propertyPath,
    ) {
    }

    public function getFqcn(): string
    {
        return $this->fqcn;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getPropertyPath(): string
    {
        return $this->propertyPath;
    }
}

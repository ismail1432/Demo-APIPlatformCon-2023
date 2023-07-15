<?php

namespace App\Http\Config;

interface ContainsApiResource
{
    /**
     * @return RelationConfiguration[]
     */
    public function getRelationsConfiguration(): array;
}

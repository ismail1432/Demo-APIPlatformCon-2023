<?php

namespace App\Http\Config;

interface DatabaseProperties
{
    /**
     * @return string[]
     */
    public function getDatabaseProperties();

    /**
     * @return RelationConfiguration[]
     */
    public function getDatabaseRelations();
}

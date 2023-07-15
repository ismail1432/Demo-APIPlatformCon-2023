<?php

namespace App\Http\Config;

interface DatabaseProperties
{
    /**
     * @return string[]
     */
    public function getFields();

    /**
     * @return OneToManyConfiguration[]
     */
    public function getOneToManyConfiguration();
}

<?php

namespace App\Http\Repository;

use App\Http\Model\User;

class HttpUserRepository extends AbstractHttpRepository
{
    public function getResourcesUri(): string
    {
        return 'users';
    }

    public static function getModelClassName(): string
    {
        return User::class;
    }
}

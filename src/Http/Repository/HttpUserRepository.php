<?php

namespace App\Http\Repository;

use App\Http\Model\User;

class HttpUserRepository extends AbstractHttpRepository
{
    public function getResourcesUri(): string
    {
        return 'users';
    }

    public function getEntityClassName(): string
    {
        return User::class;
    }
}

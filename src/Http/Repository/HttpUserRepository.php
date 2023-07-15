<?php

namespace App\Http\Repository;

use App\Entity\Utilisateur;
use App\Http\Model\User;

class HttpUserRepository extends AbstractHttpRepository
{
    public function getResourcesUri(): string
    {
        return 'users';
    }

    public function getModelClassName(): string
    {
        return User::class;
    }

    public static function getEntityClassName(): string
    {
        return Utilisateur::class;
    }
}

<?php

namespace App\Http\Config;

use App\Entity\Commande;

final class UserConfiguration implements DatabaseProperties
{
    public function getFields()
    {
        return ['email'];
    }

    public function getOneToManyConfiguration()
    {
        return [
          OneToManyConfiguration::create(Commande::class, 'utilisateur', 'commandes'),
        ];
    }
}

<?php

namespace App\Http\Config;

use App\Entity\Commande;

final class UserConfiguration implements DatabaseProperties
{
    public function getDatabaseProperties()
    {
        return ['email'];
    }

    public function getDatabaseRelations()
    {
        return [
          RelationConfiguration::create(Commande::class, 'utilisateur', 'commandes'),
        ];
    }
}

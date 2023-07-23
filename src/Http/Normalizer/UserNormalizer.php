<?php

namespace App\Http\Normalizer;

use App\Entity\Adresse;
use App\Entity\Utilisateur;
use App\Http\Config\UserConfiguration;
use App\Http\Model\User;

class UserNormalizer extends AbstractNormalizer
{
    public function doDenormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        /** @var User $user */
        $user = $this->hydrateRoot(new User(), $data);

        if (null !== $dataAddress = $data['address'] ?? null) {
            $adresse = Adresse::create(
                $dataAddress['street_name'] ?? '',
                $dataAddress['city'] ?? '',
                $dataAddress['zip_code'] ?? '',
            );
            $user->setAdresse($adresse);
        }

        $userConfiguration = new UserConfiguration();

        $this->hydrateDatabaseProperties($userConfiguration->getDatabaseProperties(), Utilisateur::class, $user);
        $this->hydrateDatabaseRelations($userConfiguration->getDatabaseRelations(), $user);

        return $user;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null)
    {
        return User::class === $type;
    }
}

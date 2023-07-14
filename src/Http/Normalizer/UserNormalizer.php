<?php

namespace App\Http\Normalizer;

use App\Entity\Adresse;
use App\Http\Model\User;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class UserNormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        /** @var User $user */
        $user = $this->hydrate(new User(), $data);

        if (null !== $dataAddress = $data['address'] ?? null) {
            $adresse = new Adresse();
            $adresse->setRue($dataAddress['street_name'])
            ->setVille($dataAddress['city'])
            ->setCodePostal($dataAddress['zip_code']);
            $user->setAdresse($adresse);
        }

        return $user;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null)
    {
        return User::class === $type;
    }

    public function hydrate($object, array $data = [])
    {
        foreach ($data as $property => $value) {
            $method = 'set'.\ucfirst($property);
            if (\method_exists($object, $method)) {
                $object->{$method}($value);
            }
        }

        $object->setId($data['@id']);

        return $object;
    }
}

<?php

namespace App\ExternalApi\Entity;

class Adress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $streetName = null;

    #[ORM\Column(length: 255)]
    private ?string $zipCode = null;
}
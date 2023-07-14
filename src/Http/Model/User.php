<?php

namespace App\Http\Model;

use App\Entity\Utilisateur;

class User extends Utilisateur
{
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setName($name)
    {
        $this->setNom($name);
    }

    public function setFirstname($firstname)
    {
        $this->setPrenom($firstname);
    }
}

<?php

namespace App\ExternalApi\Controller;

use App\ExternalApi\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class UserController
{
    #[Route('/users', name: 'users_collection')]
    public function index(UserRepository $repository): Response
    {
        $utilisateurs = $repository->findAll();

        return $this->render('utilisateur/liste.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
    }
}
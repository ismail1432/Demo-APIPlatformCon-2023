<?php

namespace App\Controller;

use App\ExternalApi\Repository\UserRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateurController extends AbstractController
{
    #[Route('/utilisateurs/liste', name: 'liste_utilisateur')]
    public function index(UtilisateurRepository $repository): Response
    {
        $utilisateurs = $repository->findAll();

        return $this->render('utilisateur/liste.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
    }

    #[Route('/utilisateurs/{id}', name: 'detail_utilisateur')]
    public function detail(UtilisateurRepository $repository, string $id): Response
    {
        $utilisateur = $repository->find($id);

        return $this->render('utilisateur/detail.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }
}

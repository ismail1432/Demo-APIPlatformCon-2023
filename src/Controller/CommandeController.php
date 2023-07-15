<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    #[Route('/commande/{id}', name: 'detail_commande')]
    public function detail(CommandeRepository $commandeRepository, string $id): Response
    {
        $commande = $commandeRepository->find($id);

        return $this->render('commande/index.html.twig', [
            'commande' => $commande,
        ]);
    }
}

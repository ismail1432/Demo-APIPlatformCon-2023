<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use App\Http\Repository\HttpUserRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Utilisateur>
 *
 * @method Utilisateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilisateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilisateur[]    findAll()
 * @method Utilisateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateurRepository extends ServiceEntityRepository
{
    private HttpUserRepository $httpUserRepository;

    public function __construct(ManagerRegistry $registry, HttpUserRepository $httpUserRepository)
    {
        parent::__construct($registry, Utilisateur::class);

        $this->httpUserRepository = $httpUserRepository;
    }

    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return $this->httpUserRepository->find($id);
    }

    public function findAll()
    {
        return $this->httpUserRepository->findAll();
    }

    public function getUtilisateursActif(): array
    {
        // blabla
    }
}

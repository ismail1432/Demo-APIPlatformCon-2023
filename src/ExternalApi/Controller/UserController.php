<?php

namespace App\ExternalApi\Controller;

use App\ExternalApi\Entity\User;
use App\ExternalApi\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class UserController
{
    #[Route('/users', name: 'users_collection')]
    public function index(UserRepository $repository): Response
    {
        /** @var User[] $users */
        $users = $repository->findAll();

        $data = [];
        foreach ($users as $user) {
            $data[] = [
                '@id' => $user->getId(),
                'name' => $user->getName(),
                'firstname' => $user->getFirstname(),
                'address' => null !== $user->getAddress() ? [
                    'street_name' => $user->getAddress()->getStreetName(),
                    'city' => $user->getAddress()->getCity(),
                    'zip_code' => $user->getAddress()->getZipCode(),
                ] : [],
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/users/{id}', name: 'users_item_get')]
    public function show(UserRepository $repository, string $id): Response
    {
        /** @var User $user */
        $user = $repository->find($id);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('User "%s" not found', $id));
        }

        $data = [
                '@id' => $user->getId(),
                'name' => $user->getName(),
                'firstname' => $user->getFirstname(),
                'address' => null !== $user->getAddress() ? [
                    'street_name' => $user->getAddress()->getStreetName(),
                    'city' => $user->getAddress()->getCity(),
                    'zip_code' => $user->getAddress()->getZipCode(),
                ] : [],
            ];

        return new JsonResponse($data);
    }
}

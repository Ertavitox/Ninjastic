<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UsersController extends AbstractController
{
    #[Route('/api/v1/user', name: 'api_v1_user_new', methods: ['POST','PUT'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json($errors, JsonResponse::HTTP_BAD_REQUEST);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'message' => 'User created successfully',
            'user' => $user
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('/api/v1/user/{id}', name: 'api_v1_user_show', methods: ['GET'])]
    public function show(User $user, SerializerInterface $serializer): JsonResponse
    {
        $userData = $serializer->serialize($user, 'json');
        return new JsonResponse($userData, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/api/v1/user/{id}', name: 'api_v1_user_edit', methods: ['PATCH'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        $serializer->deserialize($request->getContent(), User::class, 'json', ['object_to_populate' => $user]);
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json($errors, JsonResponse::HTTP_BAD_REQUEST);
        }

        $entityManager->flush();
        return $this->json(['message' => 'User updated successfully'], JsonResponse::HTTP_OK);
    }

    #[Route('/api/v1/user/{id}', name: 'api_v1_user_delete', methods: ['DELETE'])]
    public function delete(User $user, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->json(['message' => 'User deleted successfully'], JsonResponse::HTTP_OK);
    }
}

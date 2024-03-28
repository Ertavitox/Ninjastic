<?php

namespace App\Controller;

use App\Entity\User;
use App\Dto\RequestDto;
use App\Helper\ValidationErrorHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\DuplicateKeyException;

class UsersController extends AbstractController
{
    #[Route('/api/v1/user', name: 'api_v1_user_new', methods: ['POST', 'PUT'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
    ): JsonResponse {

        try {
            $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        } catch (\Exception $e) {
            return $this->json(
                new RequestDto(
                    message: 'Invalid JSON format'
                ),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json(new RequestDto(
                errors: (new ValidationErrorHelper($errors))->getTransformedErrors()
            ), JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (UniqueConstraintViolationException $e) {
            return $this->json(
                new RequestDto(
                    message: 'Email already exists',
                    errors: [
                        'key' => "email",
                        'message' => "This email is already in use"
                    ]
                ),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return $this->json([
            'message' => 'User created successfully',
            'user' => $user
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('/api/v1/user/{id}', name: 'api_v1_user_show', methods: ['GET'])]
    public function show(User $user): JsonResponse
    {
        $userData = [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'status' => $user->getStatus(),
        ];

        if ($this->getUser()->getId() !== $user->getId()) {
            return $this->json(
                new RequestDto(
                    result: $userData
                ),
                JsonResponse::HTTP_FORBIDDEN
            );
        }

        $userData['email'] = $user->getEmail();

        return new JsonResponse(new RequestDto(result: $userData));
    }

    #[Route('/api/v1/user/{id}', name: 'api_v1_user_edit', methods: ['PATCH'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        $userId = $this->getUser()->getId();

        if ($userId != $user->getId()) {
            return $this->json(
                new RequestDto(
                    message: 'You are not allowed to delete this user'
                ),
                JsonResponse::HTTP_FORBIDDEN
            );
        }

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

        $userId = $this->getUser()->getId();

        if ($userId != $user->getId()) {
            return $this->json(
                new RequestDto(
                    message: 'You are not allowed to delete this user'
                ),
                JsonResponse::HTTP_FORBIDDEN
            );
        }

        $entityManager->remove($user);
        $entityManager->flush();
        return $this->json(['message' => 'User deleted successfully'], JsonResponse::HTTP_OK);
    }
}

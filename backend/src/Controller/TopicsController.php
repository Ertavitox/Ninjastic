<?php

namespace App\Controller;

use App\Entity\Topic;
use App\Dto\RequestDto;
use App\Repository\UserRepository;
use App\Repository\TopicRepository;
use App\Helper\ValidationErrorHelper;
use App\Validator\PaginationValidator;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/v1/topic')]
class TopicsController extends AbstractController
{

    public function __construct(
        private TopicRepository $topicRepository,
        private UserRepository $userRepository,
    ) {
    }

    #[Route('', name: 'app_topic_index', methods: ['GET'])]
    public function index(
        ValidatorInterface $validator,
        #[MapQueryParameter] int $page = 1,
        #[MapQueryParameter] int $limit = 10
    ): JsonResponse {
        $paginationValidator = new PaginationValidator($page, $limit);
        $errors = $validator->validate($paginationValidator);

        if (count($errors) > 0) {
            return $this->json(
                new RequestDto(
                    message: "Validation Failed!",
                    errors: (new ValidationErrorHelper($errors))->getTransformedErrors(),
                ),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return $this->json(
            new RequestDto(result: $this->topicRepository->paginate($page, $limit))
        );
    }

    #[Route('', name: 'app_topic_new', methods: ['POST', 'PUT'])]
    public function new(
        ValidatorInterface $validator,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $topic = $serializer->deserialize($request->getContent(), Topic::class, 'json');
        $topic->setUser($this->getUser());

        $errors = $validator->validate($topic);
        if (count($errors) > 0) {
            return $this->json(
                new RequestDto(
                    message: "Failed to create new comment!",
                    errors: (new ValidationErrorHelper($errors))->getTransformedErrors(),
                ),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $entityManager->persist($topic);
        $entityManager->flush();

        return $this->json(
            new RequestDto(
                result: $topic->getId(),
                message: "Topic created successfully!"
            ),
            JsonResponse::HTTP_CREATED
        );
    }


    #[Route('/{id}', name: 'app_topic_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $topic = $this->topicRepository->findOneBy([
            'id' => $id
        ]);

        if (!$topic) {
            return $this->json(
                new RequestDto(
                    message: "Topic not found!",
                    errors: [
                        'id' => 'Invalid topic id!'
                    ],
                ),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return $this->json(
            new RequestDto(
                result: [
                    'id' => $topic->getId(),
                    'name' => $topic->getName(),
                    'description' => $topic->getDescription(),
                    'created_at' => $topic->getCreatedAt(),
                    'updated_at' => $topic->getUpdatedAt(),
                    'user_id' => $topic->getUser()->getId(),
                    'username' => $topic->getUser()->getName()
                ]
            )
        );
    }

    #[Route('/{id}', name: 'app_topic_edit', methods: ['PATCH'])]
    public function edit(
        int $id,
        ValidatorInterface $validator,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ): Response {
        $topic = $this->topicRepository->findOneBy([
            'id' => $id,
            'user' => $this->getUser()
        ]);

        if (!$topic) {
            return $this->json(
                new RequestDto(
                    message: "Topic not found!",
                    errors: [
                        'topicId' => "Invalid topic id!"
                    ],
                ),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $topicFromRequest = $serializer->deserialize($request->getContent(), Topic::class, 'json');
        $topicFromRequest->setUser($this->getUser());

        $errors = $validator->validate($topicFromRequest);
        if (count($errors) > 0) {
            return $this->json(
                new RequestDto(
                    message: "Failed to update topic!",
                    errors: (new ValidationErrorHelper($errors))->getTransformedErrors(),
                ),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $topic->setDescription($topicFromRequest->getDescription());
        $topic->setName($topicFromRequest->getName());

        $entityManager->flush();

        return $this->json(
            new RequestDto(
                result: $topic->getId(),
                message: "Comment updated successfully!"
            )
        );
    }


    #[Route('/{id}', name: 'app_topic_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $topic = $this->topicRepository->findOneBy([
            'id' => $id,
            'user' => $this->getUser()
        ]);

        if (!$topic) {
            return $this->json(
                new RequestDto(
                    message: "Topic not found!",
                    errors: [
                        'topicId' => "Invalid topic id!"
                    ],
                ),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $entityManager->remove($topic);

        return $this->json(
            new RequestDto(
                message: "Comment deleted successfully!"
            )
        );
    }
}

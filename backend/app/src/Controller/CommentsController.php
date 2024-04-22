<?php

namespace App\Controller;

use App\Dto\RequestDto;
use App\Entity\Comment;
use App\Service\WordCensor;
use App\Repository\UserRepository;
use App\Repository\TopicRepository;
use App\Helper\ValidationErrorHelper;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use App\Validator\Comments\CommentsPaginationValidator;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/v1/topics/{topicId}/comments')]
class CommentsController extends AbstractController
{
    public function __construct(
        private CommentRepository $commentRepository,
        private TopicRepository $topicRepository,
        private UserRepository $userRepository,
        private WordCensor $wordCensor
    ) {
    }

    #[Route('', name: 'app_comments_index', methods: ['GET'], host: 'api.ninjastic.pro')]
    public function index(
        ValidatorInterface $validator,
        int $topicId,
        #[MapQueryParameter] int $page = 1,
        #[MapQueryParameter] int $limit = 10
    ): JsonResponse {
        $commentsPaginationValidator = new CommentsPaginationValidator($topicId, $page, $limit);
        $errors = $validator->validate($commentsPaginationValidator);

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
            new RequestDto(result: $this->commentRepository->paginate($topicId, $page, $limit))
        );
    }

    #[Route('', name: 'app_comments_new', methods: ['POST'], host: 'api.ninjastic.pro')]
    public function new(
        int $topicId,
        ValidatorInterface $validator,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ): Response {

        $topic = $this->topicRepository->getTopicById($topicId);

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

        $comment = $serializer->deserialize($request->getContent(), Comment::class, 'json');
        $comment->setUser($this->getUser());
        $comment->setTopic($topic);
        $comment->setMessage($this->wordCensor->censorWords($comment->getOriginal()));

        $errors = $validator->validate($comment);
        if (count($errors) > 0) {
            return $this->json(
                new RequestDto(
                    message: "Failed to create new comment!",
                    errors: (new ValidationErrorHelper($errors))->getTransformedErrors(),
                ),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->json(
            new RequestDto(
                result: ['id' => $comment->getId()],
                message: "Comment created successfully!"
            ),
            JsonResponse::HTTP_CREATED
        );
    }

    #[Route('/{id}', name: 'app_comments_show', methods: ['GET'], host: 'api.ninjastic.pro')]
    public function show(
        int $topicId,
        int $id,
    ): JsonResponse {

        $topic = $this->topicRepository->getTopicById($topicId);

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

        $comment = $this->commentRepository->findOneBy([
            'id' => $id
        ]);

        if (!$comment) {
            return $this->json(
                new RequestDto(
                    message: "Comment not found!",
                    errors: [
                        'topicId' => "Invalid comment id!"
                    ],
                ),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return $this->json(
            new RequestDto(
                result: [
                    'id' => $comment->getId(),
                    'message' => $comment->getMessage(),
                    'created_at' => $comment->getCreatedAt(),
                    'updated_at' => $comment->getUpdatedAt(),
                    'user_id' => $comment->getUser()->getId(),
                    'user_name' => $comment->getUser()->getName()
                ],
            )
        );
    }


    #[Route('/{id}', name: 'app_comments_edit', methods: ['PATCH'], host: 'api.ninjastic.pro')]
    public function edit(
        int $topicId,
        int $id,
        ValidatorInterface $validator,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ): JsonResponse {

        $topic = $this->topicRepository->getTopicById($topicId);

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

        $comment = $this->commentRepository->findOneBy([
            'id' => $id,
            'user' => $this->getUser()
        ]);

        if (!$comment) {
            return $this->json(
                new RequestDto(
                    message: "Comment not found!",
                    errors: [
                        'topicId' => "Invalid comment id!"
                    ],
                ),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $commentFromRequest = $serializer->deserialize($request->getContent(), Comment::class, 'json');
        $commentFromRequest->setUser($this->getUser());
        $commentFromRequest->setTopic($topic);
        $commentFromRequest->setMessage($this->wordCensor->censorWords($comment->getOriginal()));
        $commentFromRequest->setOriginal($commentFromRequest->getOriginal());

        $errors = $validator->validate($commentFromRequest);
        if (count($errors) > 0) {
            return $this->json(
                new RequestDto(
                    message: "Failed to update comment!",
                    errors: (new ValidationErrorHelper($errors))->getTransformedErrors(),
                ),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $comment->setMessage($this->wordCensor->censorWords($commentFromRequest->getOriginal()));
        $comment->setOriginal($commentFromRequest->getOriginal());

        $entityManager->flush();

        return $this->json(
            new RequestDto(
                result: ['id' => $comment->getId()],
                message: "Comment updated successfully!"
            )
        );
    }

    #[Route('/{id}', name: 'app_comments_delete', methods: ['DELETE'], host: 'api.ninjastic.pro')]
    public function delete(
        int $topicId,
        int $id,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $topic = $this->topicRepository->getTopicById($topicId);

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

        $comment = $this->commentRepository->findOneBy([
            'id' => $id,
            'user' => $this->getUser()
        ]);

        if (!$comment) {
            return $this->json(
                new RequestDto(
                    message: "Comment not found!",
                    errors: [
                        'topicId' => "Invalid comment id!"
                    ],
                ),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->json(
            new RequestDto(
                message: "Comment deleted successfully!"
            )
        );
    }
}

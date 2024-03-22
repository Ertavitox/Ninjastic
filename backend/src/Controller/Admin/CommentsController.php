<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Topic;
use App\Entity\Comment;
use App\Helper\FlashBag;
use App\Twig\AppExtension;
use App\Form\CommentFormType;
use App\Helper\AdminHtmlDetails;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;

class CommentsController extends AdminController
{
    private CommentRepository $repository;
    public function __construct(CommentRepository $repository, RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->adminHtmlDetails = new AdminHtmlDetails(get_class());
        parent::__construct($requestStack, $entityManager);
    }

    #[Route('/admin/comments', name: 'app_admin_comments_index')]
    public function index(
        #[MapQueryParameter] int $actPage = 1,
        #[MapQueryParameter] int $pageSize = 25,
        #[MapQueryParameter] string $orderField = "id",
        #[MapQueryParameter] string $orderSort = "ASC",
        #[MapQueryParameter] string $search = "",
        #[MapQueryParameter] int $searchStatus = -1,
        #[MapQueryParameter] string $searchUsername = "",
        #[MapQueryParameter] string $searchTopic = ""
    ): Response {
        if (!$this->isAdmin()) {
            return $this->redirectToRoute('app_admin_login');
        }

        $query = $this->repository->adminListing($orderField, $orderSort, $search, $searchStatus, $searchUsername, $searchTopic);

        $this->adminHtmlDetails->setPagerData(AppExtension::AdminPager($query, $actPage, $pageSize));
        $this->adminHtmlDetails->setDefault("index", "comments", "Comments", []);
        $this->adminHtmlDetails->setExtraParameter("searchStatusModul", [
            '0' => 'Inactive',
            '1' => 'Active',
        ]);
        $this->adminHtmlDetails->setExtraParameter("breadcrumb", [
            ['name' => 'Comments']
        ]);
        $this->adminHtmlDetails->setExtraParameter("searchUserName", true);
        $this->adminHtmlDetails->setExtraParameter("searchTopicName", true);

        return $this->render("admin/comments/index.html.twig",  $this->adminHtmlDetails->getData());
    }

    #[Route('/admin/comments/create', name: 'app_admin_comments_create')]
    public function create(Request $request): Response
    {

        if (!$this->isAdmin()) {
            return $this->redirectToRoute('app_admin_login');
        }

        $entity = new Comment();
        $error = array();

        if ($request->getMethod() == "POST") {
            $formType = new CommentFormType($this->entityManager);
            $result = $formType->createUpdate($this->entityManager, $entity);
            $entity = $result['entity'];
            $error = $result['error'];
            if (empty($error) && AppExtension::checkStayPage()) {
                FlashBag::set('notice', array('title' => 'System message', 'text' => 'Successful creation!'));
                return $this->redirectToRoute('app_admin_comments_index');
            } elseif (empty($error) && !AppExtension::checkStayPage()) {
                FlashBag::set('notice', array('title' => 'System message', 'text' => 'Successful creation!'));
                return $this->redirectToRoute('app_admin_comments_edit', ["id" => $entity->getId()]);
            }
        }
        $userEM = $this->entityManager->getRepository(User::class);
        $topicEM = $this->entityManager->getRepository(Topic::class);

        $this->adminHtmlDetails->setDefault("create", "comments", 'Create Comment', $error);
        $this->adminHtmlDetails->setExtraParameter("breadcrumb", [
            ["name" => "Comments", "url" => "/admin/coments"],
            ['name' => 'Create']
        ]);
        $this->adminHtmlDetails->setExtraParameter("Entity", $entity);
        $this->adminHtmlDetails->setExtraParameter("UserList", $userEM->findAll());
        $this->adminHtmlDetails->setExtraParameter("TopicList", $topicEM->findAll());

        return $this->render("admin/comments/edit.html.twig", $this->adminHtmlDetails->getData());
    }


    #[Route('/admin/comments/edit/{id}', name: 'app_admin_comments_edit')]
    public function edit(int $id, Request $request): Response
    {
        if (!$this->isAdmin()) {
            return $this->redirectToRoute('app_admin_login');
        }

        $entity = $this->repository->find($id);
        $error = [];
        if (empty($entity)) {
            FlashBag::set('notice', array('title' => 'System message', 'text' => 'Record not found'));
            return $this->redirectToRoute('app_admin_comments_index');
        }

        if ($request->getMethod() == "POST") {
            $formType = new CommentFormType($this->entityManager);
            $result = $formType->createUpdate($this->entityManager, $entity);
            $entity = $result['entity'];
            $error = $result['error'];
            if (empty($error) && AppExtension::checkStayPage()) {
                FlashBag::set('notice', array('title' => 'System message', 'text' => 'Record updated successfully'));
                return $this->redirectToRoute('app_admin_comments_index');
            }
        }
        $userEM = $this->entityManager->getRepository(User::class);
        $topicEM = $this->entityManager->getRepository(Topic::class);

        $this->adminHtmlDetails->setDefault("edit", "comments", 'Edit Comment - ' . $entity->getId(), $error);
        $this->adminHtmlDetails->setExtraParameter("breadcrumb", [
            ["name" => "Comments", "url" => "/admin/comments"],
            ['name' => 'Edit']
        ]);
        $this->adminHtmlDetails->setExtraParameter("Entity", $entity);
        $this->adminHtmlDetails->setExtraParameter("UserList", $userEM->findAll());
        $this->adminHtmlDetails->setExtraParameter("TopicList", $topicEM->findAll());

        return $this->render("admin/comments/edit.html.twig", $this->adminHtmlDetails->getData());
    }

    #[Route('/admin/comments/delete/{id}', name: 'app_admin_comments_delete')]
    public function delete(int $id, Request $request): JsonResponse
    {
        if (!$this->isAdmin()) {
            return new JsonResponse(['error' => true]);
        }

        if ($request->getMethod() == "POST" && $request->request->get('delete') == 1) {
            $entity = $this->repository->find($id);
            if ($entity) {
                $this->entityManager->remove($entity);
                $this->entityManager->flush();
                return new JsonResponse(['success' => true]);
            } else {
                return new JsonResponse(['error' => true]);
            }
        } else {
            return new JsonResponse(['error' => true]);
        }
    }
}

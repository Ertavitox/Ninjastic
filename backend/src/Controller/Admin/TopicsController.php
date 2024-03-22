<?php

namespace App\Controller\Admin;

use App\Entity\Topic;
use App\Entity\User;
use App\Form\TopicFormType;
use App\Helper\AdminHtmlDetails;
use App\Helper\FlashBag;
use App\Repository\TopicRepository;
use App\Twig\AppExtension;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class TopicsController extends AdminController
{

    private TopicRepository $repository;
    public function __construct(TopicRepository $repository, RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->adminHtmlDetails = new AdminHtmlDetails(get_class());
        parent::__construct($requestStack, $entityManager);
    }

    #[Route('/admin/topics', name: 'app_admin_topics_index')]
    public function index(
        #[MapQueryParameter()] int $actPage = 1,
        #[MapQueryParameter] int $pageSize = 25,
        #[MapQueryParameter] string $orderField = "id",
        #[MapQueryParameter] string $orderSort = "ASC",
        #[MapQueryParameter] string $search = "",
        #[MapQueryParameter] int $searchStatus = -1,
        #[MapQueryParameter] string $searchUsername = ""
    ): Response {
        if (!$this->isAdmin()) {
            return $this->redirectToRoute('app_admin_login');
        }

        $query = $this->repository->adminListing($orderField, $orderSort, $search, $searchStatus, $searchUsername);


        $this->adminHtmlDetails->setPagerData(AppExtension::AdminPager($query, $actPage, $pageSize));
        $this->adminHtmlDetails->setDefault("index", "topics", "Topics", []);
        $this->adminHtmlDetails->setExtraParameter("searchStatusModul", [
            '0' => 'Inactive',
            '1' => 'Active',
        ]);
        $this->adminHtmlDetails->setExtraParameter("breadcrumb", [
            ['name' => 'Topics']
        ]);
        $this->adminHtmlDetails->setExtraParameter("searchUserName", true);

        return $this->render("admin/topics/index.html.twig", $this->adminHtmlDetails->getData());
    }

    #[Route('/admin/topics/create', name: 'app_admin_topics_create')]
    public function create(Request $request): Response
    {
        if (!$this->isAdmin()) {
            return $this->redirectToRoute('app_admin_login');
        }

        $entity = new Topic();
        $error = array();

        if ($request->getMethod() == "POST") {
            $formType = new TopicFormType();
            $result = $formType->createUpdate($this->entityManager, $entity);
            $entity = $result['entity'];
            $error = $result['error'];
            if (empty($error) && AppExtension::checkStayPage()) {
                FlashBag::set('notice', array('title' => 'System message', 'text' => 'Successful creation!'));
                return $this->redirectToRoute('app_admin_topics_index');
            } elseif (empty($error) && !AppExtension::checkStayPage()) {
                FlashBag::set('notice', array('title' => 'System message', 'text' => 'Successful creation!'));
                return $this->redirectToRoute('app_admin_topics_edit', ["id" => $entity->getId()]);
            }
        }
        $userEM = $this->entityManager->getRepository(User::class);

        $this->adminHtmlDetails->setDefault("create", "topics", 'Create Topic', $error);
        $this->adminHtmlDetails->setExtraParameter("breadcrumb", [
            ["name" => "Topics", "url" => "/admin/topics"],
            ['name' => 'Create']
        ]);
        $this->adminHtmlDetails->setExtraParameter("Entity", $entity);
        $this->adminHtmlDetails->setExtraParameter("UserList", $userEM->findAll());

        return $this->render("admin/topics/edit.html.twig", $this->adminHtmlDetails->getData());
    }


    #[Route('/admin/topics/edit/{id}', name: 'app_admin_topics_edit')]
    public function edit(int $id, Request $request): Response
    {
        if (!$this->isAdmin()) {
            return $this->redirectToRoute('app_admin_login');
        }

        $entity = $this->repository->find($id);
        $error = [];
        if (empty($entity)) {
            FlashBag::set('notice', array('title' => 'System message', 'text' => 'Record not found'));
            return $this->redirectToRoute('app_admin_topics_index');
        }

        if ($request->getMethod() == "POST") {
            $formType = new TopicFormType();
            $result = $formType->createUpdate($this->entityManager, $entity);
            $entity = $result['entity'];
            $error = $result['error'];
            if (empty($error) && AppExtension::checkStayPage()) {
                FlashBag::set('notice', array('title' => 'System message', 'text' => 'Record updated successfully'));
                return $this->redirectToRoute('app_admin_topics_index');
            }
        }
        $userEM = $this->entityManager->getRepository(User::class);
        $data = [];
        $data["controller_name"] = "TopicsController";
        $data["action_name"] = "edit";
        $data["controller_url"] = "topics";
        $data['page_title'] = 'Edit Topic - ' . $entity->getId();
        $data['Entity'] = $entity;
        $data['UserList'] = $userEM->findAll();
        $data['error'] = $error;
        $data['breadcrumb'] = [
            ["name" => "Topics", "url" => "/admin/topics"],
            ['name' => 'Edit']
        ];

        return $this->render("admin/topics/edit.html.twig", $data);
    }

    #[Route('/admin/topics/delete/{id}', name: 'app_admin_topics_delete')]
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

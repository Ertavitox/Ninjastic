<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserFormType;
use App\Helper\AdminHtmlDetails;
use App\Helper\FlashBag;
use App\Repository\UserRepository;
use App\Twig\AppExtension;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class UsersController extends AdminController
{

    private UserRepository $repository;
    public function __construct(UserRepository $repository, RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->adminHtmlDetails = new AdminHtmlDetails(get_class());
        parent::__construct($requestStack, $entityManager);
    }

    #[Route('/admin/users', name: 'app_admin_users_index')]
    public function index(
        #[MapQueryParameter()] int $actPage = 1,
        #[MapQueryParameter] int $pageSize = 25,
        #[MapQueryParameter] string $orderField = "id",
        #[MapQueryParameter] string $orderSort = "ASC",
        #[MapQueryParameter] string $search = "",
        #[MapQueryParameter] int $searchStatus = -1,
    ): Response {
        if (!$this->isAdmin()) {
            return $this->redirectToRoute('app_admin_login');
        }

        $query = $this->repository->adminListing($orderField, $orderSort, $search, $searchStatus);

        $this->adminHtmlDetails->setPagerData(AppExtension::AdminPager($query, $actPage, $pageSize));
        $this->adminHtmlDetails->setDefault("index", "users", "Users", []);
        $this->adminHtmlDetails->setExtraParameter("searchStatusModul", [
            '0' => 'Inactive',
            '1' => 'Active',
        ]);
        $this->adminHtmlDetails->setExtraParameter("breadcrumb", [
            ['name' => 'Users']
        ]);

        return $this->render("admin/users/index.html.twig", $this->adminHtmlDetails->getData());
    }

    #[Route('/admin/users/create', name: 'app_admin_users_create')]
    public function create(Request $request): Response
    {
        if (!$this->isAdmin()) {
            return $this->redirectToRoute('app_admin_login');
        }

        $entity = new User();
        $error = array();
        if ($request->getMethod() == "POST") {
            $formType = new UserFormType();
            $result = $formType->createUpdate($this->entityManager, $entity);
            $entity = $result['entity'];
            $error = $result['error'];
            if (empty($error) && AppExtension::checkStayPage()) {
                FlashBag::set('notice', array('title' => 'System message', 'text' => 'Successful creation!'));
                return $this->redirectToRoute('app_admin_users_index');
            } elseif (empty($error) && !AppExtension::checkStayPage()) {
                FlashBag::set('notice', array('title' => 'System message', 'text' => 'Successful creation!'));
                return $this->redirectToRoute('app_admin_users_edit', ["id" => $entity->getId()]);
            }
        }

        $this->adminHtmlDetails->setDefault("create", "users", 'Create User', $error);
        $this->adminHtmlDetails->setExtraParameter("breadcrumb", [
            ["name" => "Users", "url" => "/admin/users"],
            ['name' => 'Create']
        ]);
        $this->adminHtmlDetails->setExtraParameter("Entity", $entity);

        return $this->render("admin/users/edit.html.twig", $this->adminHtmlDetails->getData());
    }


    #[Route('/admin/users/edit/{id}', name: 'app_admin_users_edit')]
    public function edit(int $id, Request $request): Response
    {
        if (!$this->isAdmin()) {
            return $this->redirectToRoute('app_admin_login');
        }

        $entity = $this->repository->find($id);
        $error = [];
        if (empty($entity)) {
            FlashBag::set('notice', array('title' => 'System message', 'text' => 'Record not found'));
            return $this->redirectToRoute('app_admin_users_index');
        }

        if ($request->getMethod() == "POST") {
            $formType = new UserFormType();
            $result = $formType->createUpdate($this->entityManager, $entity);
            $entity = $result['entity'];
            $error = $result['error'];
            if (empty($error) && AppExtension::checkStayPage()) {
                FlashBag::set('notice', array('title' => 'System message', 'text' => 'Record updated successfully'));
                return $this->redirectToRoute('app_admin_users_index');
            }
        }

        $this->adminHtmlDetails->setDefault("edit", "users", 'Edit User - ' . $entity->getId(), $error);
        $this->adminHtmlDetails->setExtraParameter("breadcrumb", [
            ["name" => "Users", "url" => "/admin/users"],
            ['name' => 'Edit']
        ]);
        $this->adminHtmlDetails->setExtraParameter("Entity", $entity);

        return $this->render("admin/users/edit.html.twig", $this->adminHtmlDetails->getData());
    }

    #[Route('/admin/users/delete/{id}', name: 'app_admin_users_delete')]
    public function delete(int $id, Request $request): JsonResponse
    {
        if (!$this->isAdmin()) {
            return $this->redirectToRoute('app_admin_login');
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

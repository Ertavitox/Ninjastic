<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Form\AdminFormType;
use App\Helper\AdminHtmlDetails;
use App\Helper\FlashBag;
use App\Repository\AdminRepository;
use App\Twig\AppExtension;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class AdminsController extends AdminController
{
    private AdminRepository $repository;
    public function __construct(AdminRepository $repository, RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->setFormType(new AdminFormType());
        $this->setAdminHtmlDetails(new AdminHtmlDetails(get_class()));
        parent::__construct($requestStack, $entityManager);
    }

    #[Route('/admin/admins', name: 'app_admin_admins_index')]
    public function index(
        #[MapQueryParameter] int $actPage = 1,
        #[MapQueryParameter] int $pageSize = 25,
        #[MapQueryParameter] string $orderField = "id",
        #[MapQueryParameter] string $orderSort = "ASC",
        #[MapQueryParameter] string $search = "",
        #[MapQueryParameter()] int $searchStatus = -1
    ): Response {
        if (!$this->isAdmin()) {
            return $this->redirectToRoute('app_admin_login');
        }

        $query = $this->repository->adminListing($orderField, $orderSort, $search, $searchStatus);
        $this->adminHtmlDetails->setPagerData($query, $actPage, $pageSize);
        $this->adminHtmlDetails->setDefault("index", "admins", "Admins", []);
        $this->adminHtmlDetails->setExtraParameter("searchStatusModul", [
            '0' => 'Inactive',
            '1' => 'Active',
        ]);
        $this->adminHtmlDetails->setExtraParameter("breadcrumb", [
            ['name' => 'Admins']
        ]);

        return $this->render("admin/admins/index.html.twig", $this->adminHtmlDetails->getData());
    }

    #[Route('/admin/admins/create', name: 'app_admin_admins_create')]
    public function create(Request $request): Response
    {

        if (!$this->isAdmin()) {
            return $this->redirectToRoute('app_admin_login');
        }

        $entity = new Admin();
        $error = array();
        if ($request->getMethod() == "POST") {
            $result = $this->formType->createUpdate($this->entityManager, $entity);
            $entity = $result['entity'];
            $error = $result['error'];
            if (empty($error) && $this->adminHtmlDetails->checkStayPage()) {
                FlashBag::set('notice', array('title' => 'System message', 'text' => 'Successful creation!'));
                return $this->redirectToRoute('app_admin_admins_index');
            } elseif (empty($error) && !$this->adminHtmlDetails->checkStayPage()) {
                FlashBag::set('notice', array('title' => 'System message', 'text' => 'Successful creation!'));
                return $this->redirectToRoute('app_admin_admins_edit', ["id" => $entity->getId()]);
            }
        }

        $this->adminHtmlDetails->setDefault("create", "admins", 'Create Admin', $error);
        $this->adminHtmlDetails->setExtraParameter("breadcrumb", [
            ["name" => "Admins", "url" => "/admin/admins"],
            ['name' => 'Create']
        ]);
        $this->adminHtmlDetails->setExtraParameter("Entity", $entity);

        return $this->render("admin/admins/edit.html.twig", $this->adminHtmlDetails->getData());
    }


    #[Route('/admin/admins/edit/{id}', name: 'app_admin_admins_edit')]
    public function edit(int $id, Request $request): Response
    {
        if (!$this->isAdmin()) {
            return $this->redirectToRoute('app_admin_login');
        }

        $entity = $this->repository->find($id);
        $error = [];
        if (empty($entity)) {
            FlashBag::set('notice', array('title' => 'System message', 'text' => 'Record not found'));
            return $this->redirectToRoute('app_admin_admins_index');
        }

        if ($request->getMethod() == "POST") {
            $result = $this->formType->createUpdate($this->entityManager, $entity);
            $entity = $result['entity'];
            $error = $result['error'];
            if (empty($error) && $this->adminHtmlDetails->checkStayPage()) {
                FlashBag::set('notice', array('title' => 'System message', 'text' => 'Record updated successfully'));
                return $this->redirectToRoute('app_admin_admins_index');
            }
        }

        $this->adminHtmlDetails->setDefault("edit", "admins", 'Edit Admin - ' . $entity->getId(), $error);
        $this->adminHtmlDetails->setExtraParameter("breadcrumb", [
            ["name" => "Admins", "url" => "/admin/admins"],
            ['name' => 'Edit']
        ]);
        $this->adminHtmlDetails->setExtraParameter("Entity", $entity);

        return $this->render("admin/admins/edit.html.twig", $this->adminHtmlDetails->getData());
    }

    #[Route('/admin/admins/delete/{id}', name: 'app_admin_admins_delete')]
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

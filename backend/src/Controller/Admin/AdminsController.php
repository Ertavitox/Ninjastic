<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Form\AdminFormType;
use App\Helper\FlashBag;
use App\Repository\AdminRepository;
use App\Twig\AppExtension;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminsController extends AdminController
{

    #[Route('/admin/admins', name: 'app_admin_admins_index')]
    public function index(AdminRepository $adminRepository): Response
    {
        if (!$this->isAdmin()) {
            return $this->redirectToRoute('app_admin_login');
        }

        $currentPage = isset($_GET['actpage']) && $_GET['actpage'] > 0 ? $_GET['actpage'] : 1;
        $limit = isset($_GET['pagesize']) && $_GET['pagesize'] > 0 ? $_GET['pagesize'] : 25;
        $orderfield = isset($_GET['orderfield']) ? $_GET['orderfield'] : 'id';
        $ordersort = isset($_GET['ordersort']) ? $_GET['ordersort'] : 'ASC';
        $search = isset($_GET['search']) && strlen(trim($_GET['search'])) > 0 ? trim($_GET['search']) : false;
        $searchStatus = isset($_GET['searchstatus']) && strlen(trim($_GET['searchstatus'])) > 0 ? trim($_GET['searchstatus']) : false;
        $query = $adminRepository->createQueryBuilder('p')
            ->orderBy('p.' . $orderfield, $ordersort);

        if ($searchStatus !== false) {
            $filterStatus = [$searchStatus];
            $query->andWhere($query->expr()->in('p.status', $filterStatus));
        }
        if ($search) {
            $query->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('LOWER(p.name)', ':term'),
                    $query->expr()->like('LOWER(p.email)', ':term'),
                )
            )
                ->setParameter('term', '%' . strtolower($search) . '%');
        }

        $query->getQuery();

        $data = AppExtension::AdminPager($query, $currentPage, $limit);
        $data["controller_name"] = "AdminsController";
        $data["action_name"] = "index";
        $data["controller_url"] = "admins";
        $data['page_title'] = 'Admins';
        $data['searchStatusModul'] = [
            '0' => 'Inactive',
            '1' => 'Active',
        ];
        $data['breadcrumb'] = [
            ['name' => 'Admins']
        ];

        return $this->render("admin/admins/index.html.twig", $data);
    }

    #[Route('/admin/admins/create', name: 'app_admin_admins_create')]
    public function create(AdminRepository $repository, Request $request, EntityManagerInterface $entityManager): Response
    {

        if (!$this->isAdmin()) {
            return $this->redirectToRoute('app_admin_login');
        }

        $entity = new Admin();
        $error = array();
        if ($request->getMethod() == "POST") {
            $formType = new AdminFormType();
            $result = $formType->createUpdate($entityManager, $entity);
            $entity = $result['entity'];
            $error = $result['error'];
            if (empty($error) && AppExtension::checkStayPage()) {
                FlashBag::set('notice', array('title' => 'System message', 'text' => 'Successful creation!'));
                return $this->redirectToRoute('app_admin_admins_index');
            } elseif (empty($error) && !AppExtension::checkStayPage()) {
                FlashBag::set('notice', array('title' => 'System message', 'text' => 'Successful creation!'));
                return $this->redirectToRoute('app_admin_admins_edit', ["id" => $entity->getId()]);
            }
        }
        $data = [];
        $data["controller_name"] = "AdminsController";
        $data["action_name"] = "create";
        $data["controller_url"] = "admins";
        $data['page_title'] = 'Create Admin - ' . $entity->getId();
        $data['Entity'] = $entity;
        $data['error'] = $error;
        $data['breadcrumb'] = [
            ["name" => "Admins", "url" => "/admin/admins"],
            ['name' => 'Create']
        ];

        return $this->render("admin/admins/edit.html.twig", $data);
    }


    #[Route('/admin/admins/edit/{id}', name: 'app_admin_admins_edit')]
    public function edit(int $id, AdminRepository $repository, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isAdmin()) {
            return $this->redirectToRoute('app_admin_login');
        }

        $entity = $repository->find($id);
        $error = [];
        if (empty($entity)) {
            FlashBag::set('notice', array('title' => 'System message', 'text' => 'Record not found'));
            return $this->redirectToRoute('app_admin_admins_index');
        }

        if ($request->getMethod() == "POST") {
            $adminFormType = new AdminFormType();
            $result = $adminFormType->createUpdate($entityManager, $entity);
            $entity = $result['entity'];
            $error = $result['error'];
            if (empty($error) && AppExtension::checkStayPage()) {
                FlashBag::set('notice', array('title' => 'System message', 'text' => 'Record updated successfully'));
                return $this->redirectToRoute('app_admin_admins_index');
            }
        }

        $data = [];
        $data["controller_name"] = "AdminsController";
        $data["action_name"] = "edit";
        $data["controller_url"] = "admins";
        $data['page_title'] = 'Edit Admin - ' . $entity->getId();
        $data['Entity'] = $entity;
        $data['error'] = $error;
        $data['breadcrumb'] = [
            ["name" => "Admins", "url" => "/admin/admins"],
            ['name' => 'Edit']
        ];

        return $this->render("admin/admins/edit.html.twig", $data);
    }

    #[Route('/admin/admins/delete/{id}', name: 'app_admin_admins_delete')]
    public function delete(int $id, AdminRepository $repository, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$this->isAdmin()) {
            return new JsonResponse(['error' => true]);
        }

        if ($request->getMethod() == "POST" && $request->request->get('delete') == 1) {
            $entity = $repository->find($id);
            if ($entity) {
                $entityManager->remove($entity);
                $entityManager->flush();
                return new JsonResponse(['success' => true]);
            } else {
                return new JsonResponse(['error' => true]);
            }
        } else {
            return new JsonResponse(['error' => true]);
        }
    }
}

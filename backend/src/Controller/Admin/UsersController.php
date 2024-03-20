<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserFormType;
use App\Helper\FlashBag;
use App\Repository\UserRepository;
use App\Twig\AppExtension;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UsersController extends AbstractController
{

    #[Route('/admin/users', name: 'app_admin_users_index')]
    public function index(UserRepository $repository): Response
    {
        $currentPage = isset($_GET['actpage']) && $_GET['actpage'] > 0 ? $_GET['actpage'] : 1;
        $limit = isset($_GET['pagesize']) && $_GET['pagesize'] > 0 ? $_GET['pagesize'] : 25;
        $orderfield = isset($_GET['orderfield']) ? $_GET['orderfield'] : 'id';
        $ordersort = isset($_GET['ordersort']) ? $_GET['ordersort'] : 'ASC';
        $search = isset($_GET['search']) && strlen(trim($_GET['search'])) > 0 ? trim($_GET['search']) : false;
        $searchStatus = isset($_GET['searchstatus']) && strlen(trim($_GET['searchstatus'])) > 0 ? trim($_GET['searchstatus']) : false;
        $query = $repository->createQueryBuilder('p')
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
        $data["controller_name"] = "UsersController";
        $data["action_name"] = "index";
        $data["controller_url"] = "users";
        $data['page_title'] = 'Users';
        $data['searchStatusModul'] = [
            '0' => 'Inactive',
            '1' => 'Active',
        ];
        $data['breadcrumb'] = [
            ['name' => 'Users']
        ];

        return $this->render("admin/users/index.html.twig", $data);
    }

    #[Route('/admin/users/create', name: 'app_admin_users_create')]
    public function create(UserRepository $repository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $entity = new User();
        $error = array();
        if ($request->getMethod() == "POST") {
            $formType = new UserFormType();
            $result = $formType->createUpdate($entityManager, $entity);
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
        $data = [];
        $data["controller_name"] = "UsersController";
        $data["action_name"] = "create";
        $data["controller_url"] = "users";
        $data['page_title'] = 'Create User - ' . $entity->getId();
        $data['Entity'] = $entity;
        $data['error'] = $error;
        $data['breadcrumb'] = [
            ["name" => "Users", "url" => "/admin/users"],
            ['name' => 'Create']
        ];

        return $this->render("admin/users/edit.html.twig", $data);
    }


    #[Route('/admin/users/edit/{id}', name: 'app_admin_users_edit')]
    public function edit(int $id, UserRepository $repository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $entity = $repository->find($id);
        $error = [];
        if (empty($entity)) {
            FlashBag::set('notice', array('title' => 'System message', 'text' => 'Record not found'));
            return $this->redirectToRoute('app_admin_users_index');
        }

        if ($request->getMethod() == "POST") {
            $formType = new UserFormType();
            $result = $formType->createUpdate($entityManager, $entity);
            $entity = $result['entity'];
            $error = $result['error'];
            if (empty($error) && AppExtension::checkStayPage()) {
                FlashBag::set('notice', array('title' => 'Rendszerüzenet', 'text' => 'Record updated successfully'));
                return $this->redirectToRoute('app_admin_users_index');
            }
        }

        $data = [];
        $data["controller_name"] = "UsersController";
        $data["action_name"] = "edit";
        $data["controller_url"] = "users";
        $data['page_title'] = 'Edit User - ' . $entity->getId();
        $data['Entity'] = $entity;
        $data['error'] = $error;
        $data['breadcrumb'] = [
            ["name" => "Users", "url" => "/admin/users"],
            ['name' => 'Edit']
        ];

        return $this->render("admin/users/edit.html.twig", $data);
    }

    #[Route('/admin/users/delete/{id}', name: 'app_admin_users_delete')]
    public function delete(int $id, UserRepository $repository, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
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

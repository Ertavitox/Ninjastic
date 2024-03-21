<?php

namespace App\Controller\Admin;

use App\Entity\Topic;
use App\Entity\User;
use App\Form\TopicFormType;
use App\Helper\FlashBag;
use App\Repository\TopicRepository;
use App\Twig\AppExtension;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TopicsController extends AdminController
{

    #[Route('/admin/topics', name: 'app_admin_topics_index')]
    public function index(TopicRepository $repository, EntityManagerInterface $entityManager): Response
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
        $searchUserName = isset($_GET['searchusername']) && strlen(trim($_GET['searchusername'])) > 0 ? trim($_GET['searchusername']) : false;
        $userEM = $entityManager->getRepository(User::class);
        $query = $repository->createQueryBuilder('p')
            ->orderBy('p.' . $orderfield, $ordersort);

        if ($searchStatus !== false) {
            $filterStatus = [$searchStatus];
            $query->andWhere($query->expr()->in('p.status', $filterStatus));
        }

        if ($searchUserName) {
            $queryBuilder = $userEM->createQueryBuilder("u");
            $queryBuilder
                ->where(
                    $query->expr()->like('LOWER(u.name)', ':name'),
                )
                ->setParameter('name', '%' . strtolower($searchUserName) . '%');

            $usersEntity = $queryBuilder->getQuery()->getResult();
            $query->andWhere('p.user IN (:searchUser)')->setParameter('searchUser', $usersEntity);
        }

        if ($search) {
            $query->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('LOWER(p.name)', ':term'),
                    $query->expr()->like('LOWER(p.description)', ':term'),
                )
            )
                ->setParameter('term', '%' . strtolower($search) . '%');
        }

        $query->getQuery();

        $data = AppExtension::AdminPager($query, $currentPage, $limit);
        $data["controller_name"] = "TopicsController";
        $data["action_name"] = "index";
        $data["controller_url"] = "topics";
        $data["searchUserName"] = true;
        $data['page_title'] = 'Topics';
        $data['searchStatusModul'] = [
            '0' => 'Inactive',
            '1' => 'Active',
        ];
        $data['breadcrumb'] = [
            ['name' => 'Topics']
        ];

        return $this->render("admin/topics/index.html.twig", $data);
    }

    #[Route('/admin/topics/create', name: 'app_admin_topics_create')]
    public function create(TopicRepository $repository, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isAdmin()) {
            return $this->redirectToRoute('app_admin_login');
        }

        $entity = new Topic();
        $error = array();

        if ($request->getMethod() == "POST") {
            $formType = new TopicFormType();
            $result = $formType->createUpdate($entityManager, $entity);
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
        $userEM = $entityManager->getRepository(User::class);
        $data = [];
        $data["controller_name"] = "TopicsController";
        $data["action_name"] = "create";
        $data["controller_url"] = "topics";
        $data['page_title'] = 'Create Topic - ' . $entity->getId();
        $data['Entity'] = $entity;
        $data['UserList'] = $userEM->findAll();
        $data['error'] = $error;
        $data['breadcrumb'] = [
            ["name" => "Topics", "url" => "/admin/topics"],
            ['name' => 'Create']
        ];

        return $this->render("admin/topics/edit.html.twig", $data);
    }


    #[Route('/admin/topics/edit/{id}', name: 'app_admin_topics_edit')]
    public function edit(int $id, TopicRepository $repository, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isAdmin()) {
            return $this->redirectToRoute('app_admin_login');
        }

        $entity = $repository->find($id);
        $error = [];
        if (empty($entity)) {
            FlashBag::set('notice', array('title' => 'System message', 'text' => 'Record not found'));
            return $this->redirectToRoute('app_admin_topics_index');
        }

        if ($request->getMethod() == "POST") {
            $formType = new TopicFormType();
            $result = $formType->createUpdate($entityManager, $entity);
            $entity = $result['entity'];
            $error = $result['error'];
            if (empty($error) && AppExtension::checkStayPage()) {
                FlashBag::set('notice', array('title' => 'System message', 'text' => 'Record updated successfully'));
                return $this->redirectToRoute('app_admin_topics_index');
            }
        }
        $userEM = $entityManager->getRepository(User::class);
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
    public function delete(int $id, TopicRepository $repository, Request $request, EntityManagerInterface $entityManager): JsonResponse
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

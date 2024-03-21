<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Entity\Topic;
use App\Entity\User;
use App\Form\CommentFormType;
use App\Helper\FlashBag;
use App\Repository\CommentRepository;
use App\Twig\AppExtension;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CommentsController extends AdminController
{

    #[Route('/admin/comments', name: 'app_admin_comments_index')]
    public function index(CommentRepository $repository, EntityManagerInterface $entityManager): Response
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
        $searchTopic = isset($_GET['searchtopic']) && strlen(trim($_GET['searchtopic'])) > 0 ? trim($_GET['searchtopic']) : false;
        $userEM = $entityManager->getRepository(User::class);
        $topicEM = $entityManager->getRepository(Topic::class);
        $query = $repository->createQueryBuilder('p')
            ->where('1=1')
            ->orderBy('p.' . $orderfield, $ordersort);

        if ($searchStatus !== false) {
            $filterStatus = [$searchStatus];
            $query->andWhere($query->expr()->in('p.status', $filterStatus));
        }

        if ($searchUserName) {
            $userQueryBuilder = $userEM->createQueryBuilder("u");
            $userQueryBuilder
                ->where(
                    $query->expr()->like('LOWER(u.name)', ':userName'),
                )
                ->setParameter('userName', '%' . strtolower($searchUserName) . '%');

            $usersEntity = $userQueryBuilder->getQuery()->getResult();
            $query->andWhere('p.user IN (:searchUser)')->setParameter('searchUser', $usersEntity);
        }

        if ($searchTopic) {
            $topicQueryBuilder = $topicEM->createQueryBuilder("t");
            $topicQueryBuilder
                ->where(
                    $query->expr()->like('LOWER(t.name)', ':topicName'),
                )
                ->setParameter('topicName', '%' . strtolower($searchTopic) . '%');

            $topicsEntity = $topicQueryBuilder->getQuery()->getResult();
            $query->andWhere('p.topic IN (:searchTopic)')->setParameter('searchTopic', $topicsEntity);
        }

        if ($search) {
            $query->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('LOWER(p.message)', ':term'),
                )
            )
                ->setParameter('term', '%' . strtolower($search) . '%');
        }

        $query->getQuery();

        $data = AppExtension::AdminPager($query, $currentPage, $limit);
        $data["controller_name"] = "CommentsController";
        $data["action_name"] = "index";
        $data["controller_url"] = "comments";
        $data["searchUserName"] = true;
        $data["searchTopicName"] = true;
        $data['page_title'] = 'Comments';
        $data['searchStatusModul'] = [
            '0' => 'Inactive',
            '1' => 'Active',
        ];
        $data['breadcrumb'] = [
            ['name' => 'Comments']
        ];

        return $this->render("admin/comments/index.html.twig", $data);
    }

    #[Route('/admin/comments/create', name: 'app_admin_comments_create')]
    public function create(CommentRepository $repository, Request $request, EntityManagerInterface $entityManager): Response
    {

        if (!$this->isAdmin()) {
            return $this->redirectToRoute('app_admin_login');
        }

        $entity = new Comment();
        $error = array();

        if ($request->getMethod() == "POST") {
            $formType = new CommentFormType();
            $result = $formType->createUpdate($entityManager, $entity);
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
        $userEM = $entityManager->getRepository(User::class);
        $topicEM = $entityManager->getRepository(Topic::class);
        $data = [];
        $data["controller_name"] = "CommentsController";
        $data["action_name"] = "create";
        $data["controller_url"] = "comments";
        $data['page_title'] = 'Create Comment - ' . $entity->getId();
        $data['Entity'] = $entity;
        $data['UserList'] = $userEM->findAll();
        $data['TopicList'] = $topicEM->findAll();
        $data['error'] = $error;
        $data['breadcrumb'] = [
            ["name" => "Comments", "url" => "/admin/coments"],
            ['name' => 'Create']
        ];

        return $this->render("admin/comments/edit.html.twig", $data);
    }


    #[Route('/admin/comments/edit/{id}', name: 'app_admin_comments_edit')]
    public function edit(int $id, CommentRepository $repository, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isAdmin()) {
            return $this->redirectToRoute('app_admin_login');
        }

        $entity = $repository->find($id);
        $error = [];
        if (empty($entity)) {
            FlashBag::set('notice', array('title' => 'System message', 'text' => 'Record not found'));
            return $this->redirectToRoute('app_admin_comments_index');
        }

        if ($request->getMethod() == "POST") {
            $formType = new CommentFormType();
            $result = $formType->createUpdate($entityManager, $entity);
            $entity = $result['entity'];
            $error = $result['error'];
            if (empty($error) && AppExtension::checkStayPage()) {
                FlashBag::set('notice', array('title' => 'System message', 'text' => 'Record updated successfully'));
                return $this->redirectToRoute('app_admin_comments_index');
            }
        }
        $userEM = $entityManager->getRepository(User::class);
        $topicEM = $entityManager->getRepository(Topic::class);
        $data = [];
        $data["controller_name"] = "CommentsController";
        $data["action_name"] = "edit";
        $data["controller_url"] = "comments";
        $data['page_title'] = 'Edit Comment - ' . $entity->getId();
        $data['Entity'] = $entity;
        $data['UserList'] = $userEM->findAll();
        $data['TopicList'] = $topicEM->findAll();
        $data['error'] = $error;
        $data['breadcrumb'] = [
            ["name" => "Comments", "url" => "/admin/comments"],
            ['name' => 'Edit']
        ];

        return $this->render("admin/comments/edit.html.twig", $data);
    }

    #[Route('/admin/comments/delete/{id}', name: 'app_admin_comments_delete')]
    public function delete(int $id, CommentRepository $repository, Request $request, EntityManagerInterface $entityManager): JsonResponse
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

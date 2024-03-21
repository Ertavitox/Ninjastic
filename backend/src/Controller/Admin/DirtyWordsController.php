<?php

namespace App\Controller\Admin;

use App\Entity\DirtyWord;
use App\Form\DirtyWordFormType;
use App\Helper\FlashBag;
use App\Repository\DirtyWordRepository;
use App\Service\XmlProcessor;
use App\Twig\AppExtension;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DirtyWordsController extends AbstractController
{

    #[Route('/admin/dirtywords', name: 'app_admin_dirtywords_index')]
    public function index(DirtyWordRepository $adminRepository): Response
    {
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
            $query->andWhere($query->expr()->in('p.type', $filterStatus));
        }
        if ($search) {
            $query->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('LOWER(p.word)', ':term'),
                )
            )
                ->setParameter('term', '%' . strtolower($search) . '%');
        }

        $query->getQuery();

        $data = AppExtension::AdminPager($query, $currentPage, $limit);
        $data["controller_name"] = "DirtyWordsController";
        $data["action_name"] = "index";
        $data["controller_url"] = "dirtywords";
        $data['page_title'] = 'Dirty Words';
        $data['searchStatusModul'] = [
            'm' => 'Maybe Dirty Word',
            'f' => 'Force Dirty Word',
        ];
        $data['breadcrumb'] = [
            ['name' => 'Dirty Words']
        ];

        return $this->render("admin/dirtywords/index.html.twig", $data);
    }

    #[Route('/admin/dirtywords/create', name: 'app_admin_dirtywords_create')]
    public function create(DirtyWordRepository $repository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $entity = new DirtyWord();
        $error = array();
        if ($request->getMethod() == "POST") {
            $formType = new DirtyWordFormType();
            $result = $formType->createUpdate($entityManager, $entity);
            $entity = $result['entity'];
            $error = $result['error'];
            if (empty($error) && AppExtension::checkStayPage()) {
                FlashBag::set('notice', array('title' => 'System message', 'text' => 'Successful creation!'));
                return $this->redirectToRoute('app_admin_dirtywords_index');
            } elseif (empty($error) && !AppExtension::checkStayPage()) {
                FlashBag::set('notice', array('title' => 'System message', 'text' => 'Successful creation!'));
                return $this->redirectToRoute('app_admin_dirtywords_edit', ["id" => $entity->getId()]);
            }
        }
        $data = [];
        $data["controller_name"] = "DirtyWordsController";
        $data["action_name"] = "create";
        $data["controller_url"] = "dirtywords";
        $data['page_title'] = 'Create Dirty Word - ' . $entity->getId();
        $data['Entity'] = $entity;
        $data['error'] = $error;
        $data['breadcrumb'] = [
            ["name" => "Dirty Words", "url" => "/admin/dirtywords"],
            ['name' => 'Create']
        ];

        return $this->render("admin/dirtywords/edit.html.twig", $data);
    }


    #[Route('/admin/dirtywords/edit/{id}', name: 'app_admin_dirtywords_edit')]
    public function edit(int $id, DirtyWordRepository $repository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $entity = $repository->find($id);
        $error = [];
        if (empty($entity)) {
            FlashBag::set('notice', array('title' => 'System message', 'text' => 'Record not found'));
            return $this->redirectToRoute('app_admin_dirtywords_index');
        }

        if ($request->getMethod() == "POST") {
            $formType = new DirtyWordFormType();
            $result = $formType->createUpdate($entityManager, $entity);
            $entity = $result['entity'];
            $error = $result['error'];
            if (empty($error) && AppExtension::checkStayPage()) {
                FlashBag::set('notice', array('title' => 'System message', 'text' => 'Record updated successfully'));
                return $this->redirectToRoute('app_admin_dirtywords_index');
            }
        }

        $data = [];
        $data["controller_name"] = "DirtyWordsController";
        $data["action_name"] = "edit";
        $data["controller_url"] = "dirtywords";
        $data['page_title'] = 'Edit Dirty Words - ' . $entity->getId();
        $data['Entity'] = $entity;
        $data['error'] = $error;
        $data['breadcrumb'] = [
            ["name" => "Dirty Words", "url" => "/admin/dirtywords"],
            ['name' => 'Edit']
        ];

        return $this->render("admin/dirtywords/edit.html.twig", $data);
    }

    #[Route('/admin/dirtywords/delete/{id}', name: 'app_admin_dirtywords_delete')]
    public function delete(int $id, DirtyWordRepository $repository, Request $request, EntityManagerInterface $entityManager): JsonResponse
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

    #[Route('/admin/dirtywords/loadxml', name: 'app_admin_dirtywords_loadxml')]
    public function loadXml(XmlProcessor $xmlProcessor): Response
    {
        $xmlPath = getcwd() . '/dirtywords.xml';
        $xmlProcessor->process($xmlPath);

        return new Response('XML processed and saved into the database.');
    }
}

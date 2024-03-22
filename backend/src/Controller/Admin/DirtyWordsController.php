<?php

namespace App\Controller\Admin;

use App\Entity\DirtyWord;
use App\Form\DirtyWordFormType;
use App\Helper\AdminHtmlDetails;
use App\Helper\FlashBag;
use App\Repository\DirtyWordRepository;
use App\Service\XmlProcessor;
use App\Twig\AppExtension;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class DirtyWordsController extends AdminController
{

    private DirtyWordRepository $repository;
    public function __construct(DirtyWordRepository $repository, RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->adminHtmlDetails = new AdminHtmlDetails(get_class());
        parent::__construct($requestStack, $entityManager);
    }

    #[Route('/admin/dirtywords', name: 'app_admin_dirtywords_index')]
    public function index(
        #[MapQueryParameter()] int $actPage = 1,
        #[MapQueryParameter] int $pageSize = 25,
        #[MapQueryParameter] string $orderField = "id",
        #[MapQueryParameter] string $orderSort = "ASC",
        #[MapQueryParameter] string $search = "",
        #[MapQueryParameter] string $searchStatus = "-1"
    ): Response {
        if (!$this->isAdmin()) {
            return $this->redirectToRoute('app_admin_login');
        }

        $query = $this->repository->adminListing($orderField, $orderSort, $search, $searchStatus);


        $this->adminHtmlDetails->setPagerData(AppExtension::AdminPager($query, $actPage, $pageSize));
        $this->adminHtmlDetails->setDefault("index", "dirtywords", "Dirty Words", []);
        $this->adminHtmlDetails->setExtraParameter("searchStatusModul", [
            'm' => 'Maybe Dirty Word',
            'f' => 'Force Dirty Word',
        ]);
        $this->adminHtmlDetails->setExtraParameter("breadcrumb", [
            ['name' => 'Dirty Words']
        ]);
        $this->adminHtmlDetails->setExtraParameter("searchUserName", true);
        $this->adminHtmlDetails->setExtraParameter("searchTopicName", true);

        return $this->render("admin/dirtywords/index.html.twig", $this->adminHtmlDetails->getData());
    }

    #[Route('/admin/dirtywords/create', name: 'app_admin_dirtywords_create')]
    public function create(Request $request): Response
    {
        if (!$this->isAdmin()) {
            return $this->redirectToRoute('app_admin_login');
        }

        $entity = new DirtyWord();
        $error = array();
        if ($request->getMethod() == "POST") {
            $formType = new DirtyWordFormType();
            $result = $formType->createUpdate($this->entityManager, $entity);
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

        $this->adminHtmlDetails->setDefault("create", "dirtywords", 'Create Dirty Word', $error);
        $this->adminHtmlDetails->setExtraParameter("breadcrumb", [
            ["name" => "Dirty Words", "url" => "/admin/dirtywords"],
            ['name' => 'Create']
        ]);
        $this->adminHtmlDetails->setExtraParameter("Entity", $entity);

        return $this->render("admin/dirtywords/edit.html.twig", $this->adminHtmlDetails->getData());
    }


    #[Route('/admin/dirtywords/edit/{id}', name: 'app_admin_dirtywords_edit')]
    public function edit(int $id, Request $request): Response
    {
        if (!$this->isAdmin()) {
            return $this->redirectToRoute('app_admin_login');
        }

        $entity = $this->repository->find($id);
        $error = [];
        if (empty($entity)) {
            FlashBag::set('notice', array('title' => 'System message', 'text' => 'Record not found'));
            return $this->redirectToRoute('app_admin_dirtywords_index');
        }

        if ($request->getMethod() == "POST") {
            $formType = new DirtyWordFormType();
            $result = $formType->createUpdate($this->entityManager, $entity);
            $entity = $result['entity'];
            $error = $result['error'];
            if (empty($error) && AppExtension::checkStayPage()) {
                FlashBag::set('notice', array('title' => 'System message', 'text' => 'Record updated successfully'));
                return $this->redirectToRoute('app_admin_dirtywords_index');
            }
        }

        $this->adminHtmlDetails->setDefault("edit", "dirtywords", 'Edit Dirty Words - ' . $entity->getId(), $error);
        $this->adminHtmlDetails->setExtraParameter("breadcrumb", [
            ["name" => "Dirty Words", "url" => "/admin/dirtywords"],
            ['name' => 'Edit']
        ]);
        $this->adminHtmlDetails->setExtraParameter("Entity", $entity);

        return $this->render("admin/dirtywords/edit.html.twig", $this->adminHtmlDetails->getData());
    }

    #[Route('/admin/dirtywords/delete/{id}', name: 'app_admin_dirtywords_delete')]
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

    #[Route('/admin/dirtywords/loadxml', name: 'app_admin_dirtywords_loadxml')]
    public function loadXml(XmlProcessor $xmlProcessor): Response
    {
        $xmlPath = getcwd() . '/dirtywords.xml';
        $xmlProcessor->process($xmlPath);

        return new Response('XML processed and saved into the database.');
    }
}

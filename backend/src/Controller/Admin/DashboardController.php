<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Entity\Comment;
use App\Entity\Topic;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AdminController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$this->isAdmin()) {
            return $this->redirectToRoute('app_admin_login');
        }

        $userEM = $entityManager->getRepository(User::class);
        $topicEM = $entityManager->getRepository(Topic::class);
        $commentEM = $entityManager->getRepository(Comment::class);
        $adminEM = $entityManager->getRepository(Admin::class);

        return $this->render("admin/dashboard/index.html.twig", [
            "controller_name" => "DashboardController",
            "page_title" => "Dashboard",
            "usersCount" => $userEM->countAll(),
            "topicsCount" => $topicEM->countAll(),
            "commentsCount" => $commentEM->countAll(),
            "adminsCount" => $adminEM->countAll(),
            "breadcrumb" => [
                ["name" => "Dashboard"]
            ]
        ]);
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Entity\Comment;
use App\Entity\Topic;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $session = $request->getSession();
        $admin = new Admin();
        $admin->setId(1);
        $admin->setName("Jancsik Balázs");
        $admin->setEmail("jancsik.balazs@gmail.com");
        $admin->setStatus("1");
        $session->set("__admin", $admin);

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

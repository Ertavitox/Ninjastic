<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        $admin = new Admin();
        $admin->setId(1);
        $admin->setName("Jancsik Balázs");
        $admin->setEmail("jancsik.balazs@gmail.com");
        $admin->setStatus("1");
        $session->set("__admin", $admin);

        return $this->render("admin/dashboard/index.html.twig", [
            "controller_name" => "DashboardController",
            "page_title" => "Dashboard",
            "breadcrumb" => [
                ["name" => "Users"]
            ]
        ]);
    }
}

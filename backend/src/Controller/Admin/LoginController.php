<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Repository\AdminRepository;
use App\Twig\AppExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AdminController
{
    #[Route('/admin/login', name: 'app_admin_login')]
    public function login(SessionInterface $session, AdminRepository $repository, Request $request): Response
    {
        $error = [];
        if ($this->isAdmin()) {
            return $this->redirectToRoute('app_admin_dashboard');
        }

        if ($request->getMethod() == "POST") {
            $adminEntity = $repository->login($_POST["email"], $_POST["password"]);
            if ($adminEntity instanceof Admin) {
                $session->set("__admin", $adminEntity);
                return $this->redirectToRoute('app_admin_dashboard');
            }
            $error["login"] = "The email address or password is incorrect";
        }

        return $this->render('admin/login.html.twig', [
            'controller_name' => 'LoginController',
            'pageName' => AppExtension::getHostNameMain(),
            'page_title' => 'Ninjastic Login - Admin',
            'error' => $error,
        ]);
    }

    #[Route('/admin/logout', name: 'app_admin_logout')]
    public function logout(SessionInterface $session): Response
    {
        $session->remove("__admin");
        return $this->redirectToRoute('app_admin_login');
    }
}

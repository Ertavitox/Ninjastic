<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/')]
class SiteController extends AbstractController
{
    #[Route('',host: 'admin.ninjastic.pro')]
    public function index()
    {
        return $this->redirectToRoute('app_admin_login');
    }
}

<?php

namespace App\Controller\Admin;

use App\Helper\AdminHtmlDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;

class AdminController extends AbstractController
{
    protected RequestStack $requestStack;
    protected EntityManagerInterface $entityManager;
    protected AdminHtmlDetails $adminHtmlDetails;
    protected $formType;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }
    public function isAdmin(): bool
    {
        $session = $this->requestStack->getSession();
        return $session->has("__admin");
    }

    public function setAdminHtmlDetails(AdminHtmlDetails $adminHtmlDetails): void
    {
        $this->adminHtmlDetails = $adminHtmlDetails;
    }

    public function setFormType($formType): void
    {
        $this->formType = $formType;
    }
}

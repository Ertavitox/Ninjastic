<?php

use App\Controller\Admin\AdminController;
use App\Form\AdminFormType;
use App\Helper\AdminHtmlDetails;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class AdminControllerTest extends TestCase
{
    private $controller;
    private $entityManagerMock;
    private RequestStack $requestStack;
    private Session $session;

    protected function setUp(): void
    {
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->session = new Session(new MockArraySessionStorage());
        $this->requestStack = new RequestStack();
        $request = new Request();
        $request->setSession($this->session);
        $this->requestStack->push($request);

        $this->controller = new AdminController($this->requestStack, $this->entityManagerMock);
    }

    public function testIsAdminWhenAdmin(): void
    {
        $this->session->set('__admin', true);

        $this->assertTrue($this->controller->isAdmin());
    }

    public function testIsAdminWhenNotAdmin(): void
    {
        $this->assertFalse($this->controller->isAdmin());
    }

    public function testSetAdminHtmlDetails(): void
    {
        $adminHtmlDetails = $this->createMock(AdminHtmlDetails::class);
        $this->controller->setAdminHtmlDetails($adminHtmlDetails);

        $reflection = new ReflectionClass($this->controller);
        $property = $reflection->getProperty('adminHtmlDetails');
        $property->setAccessible(true);

        $this->assertSame($adminHtmlDetails, $property->getValue($this->controller));
    }

    public function testSetFormType(): void
    {
        $formType = $this->createMock(AdminFormType::class);
        $this->controller->setFormType($formType);

        $reflection = new ReflectionClass($this->controller);
        $property = $reflection->getProperty('formType');
        $property->setAccessible(true);

        $this->assertSame($formType, $property->getValue($this->controller));
    }
}

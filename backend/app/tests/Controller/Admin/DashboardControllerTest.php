<?php

namespace App\Tests\Controller\Admin;


use App\Controller\Admin\DashboardController;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class DashboardControllerTest extends TestCase
{
    private $dashboardController;
    private $requestStack;
    private $session;
    private $entityManagerMock;

    protected function setUp(): void
    {
        $this->session = new Session(new MockArraySessionStorage());
        $this->requestStack = new RequestStack();
        $request = new Request();
        $request->setSession($this->session);
        $this->requestStack->push($request);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $this->dashboardController = $this->getMockBuilder(DashboardController::class)
            ->setConstructorArgs([$this->requestStack, $this->entityManagerMock])
            ->onlyMethods(['isAdmin', 'render', 'redirectToRoute'])
            ->getMock();
    }

    public function testIndexWhenAdmin(): void
    {
        $this->dashboardController->method('isAdmin')->willReturn(true);
        $this->dashboardController->expects($this->once())
            ->method('render')
            ->with($this->equalTo('admin/dashboard/index.html.twig'))
            ->willReturn(new Response());

        $response = $this->dashboardController->index();
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testIndexWhenNotAdmin(): void
    {
        $this->dashboardController->method('isAdmin')->willReturn(false);
        $this->dashboardController->expects($this->once())
            ->method('redirectToRoute')
            ->with($this->equalTo('app_admin_login'))
            ->willReturn(new RedirectResponse('/admin/login'));

        $response = $this->dashboardController->index();
        $this->assertEquals(302, $response->getStatusCode());
    }
}

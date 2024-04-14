<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\LoginController;
use App\Entity\Admin;
use App\Repository\AdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class LoginControllerTest extends TestCase
{
    private $adminRepositoryMock;
    private $loginController;
    private $requestStack;
    private $session;
    private $entityManagerMock;

    protected function setUp(): void
    {
        $this->adminRepositoryMock = $this->createMock(AdminRepository::class);
        $this->session = new Session(new MockArraySessionStorage());
        $this->requestStack = new RequestStack();
        $request = new Request();
        $request->setSession($this->session);
        $this->requestStack->push($request);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $this->loginController = $this->getMockBuilder(LoginController::class)
            ->setConstructorArgs([$this->requestStack, $this->entityManagerMock])
            ->onlyMethods(['isAdmin', 'render', 'redirectToRoute'])
            ->getMock();
    }

    public function testIndexWhenNotAdmin(): void
    {
        $this->loginController->method('isAdmin')->willReturn(false);
        $this->loginController->expects($this->once())
            ->method('render')
            ->with($this->equalTo('admin/login.html.twig'))
            ->willReturn(new Response());

        $response = $this->loginController->login($this->session, $this->adminRepositoryMock, new Request());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testIndexWhenNotAdminWithPostAndIsAdmin(): void
    {
        $admin = new Admin();
        $admin->setId(1);
        $_POST["email"] = "test@gmail.com";
        $_POST["password"] = "12445";
        $this->adminRepositoryMock->method("login")->willReturn($admin);
        $this->loginController->method('isAdmin')->willReturn(false);
        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->loginController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_dashboard')
            ->willReturn(new RedirectResponse('/admin'));

        $response = $this->loginController->login($this->session, $this->adminRepositoryMock, $request);
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testIndexWhenNotAdminWithPostAndNotAdmin(): void
    {
        $_POST["email"] = "test@gmail.com";
        $_POST["password"] = "12445";
        $this->adminRepositoryMock->method("login")->willReturn(null);
        $this->loginController->method('isAdmin')->willReturn(false);
        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);

        $this->loginController->expects($this->once())
            ->method('render')
            ->with($this->equalTo('admin/login.html.twig'))
            ->willReturn(new Response());

        $response = $this->loginController->login($this->session, $this->adminRepositoryMock, $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testIndexWhenLogged(): void
    {
        $this->loginController->method('isAdmin')->willReturn(true);
        $this->loginController->expects($this->once())
            ->method('redirectToRoute')
            ->with($this->equalTo('app_admin_dashboard'))
            ->willReturn(new RedirectResponse('/admin'));

        $response = $this->loginController->login($this->session, $this->adminRepositoryMock, new Request());
        $this->assertEquals(302, $response->getStatusCode());
    }
}

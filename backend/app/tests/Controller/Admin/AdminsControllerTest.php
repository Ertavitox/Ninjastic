<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\AdminsController;
use App\Entity\Admin;
use App\Form\AdminFormType;
use App\Helper\AdminHtmlDetails;
use App\Repository\AdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class AdminsControllerTest extends TestCase
{
    private $adminsController;
    private $adminRepositoryMock;
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

        $this->adminsController = $this->getMockBuilder(AdminsController::class)
            ->setConstructorArgs([$this->adminRepositoryMock, $this->requestStack, $this->entityManagerMock])
            ->onlyMethods(['isAdmin', 'render', 'redirectToRoute'])
            ->getMock();
    }

    public function testIndexWhenAdmin(): void
    {
        $queryMock = $this->createMock(Query::class);
        $queryMock->method('getSQL')->willReturn('SELECT something');

        $this->adminRepositoryMock->method('adminListing')->willReturn($queryMock);
        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->expects($this->once())
            ->method('setPagerData')
            ->with($this->equalTo($queryMock), $this->equalTo(1), $this->equalTo(25));

        $this->adminsController->setAdminHtmlDetails($adminHtmlDetailsMock);
        $this->adminsController->method('isAdmin')->willReturn(true);

        $this->adminsController->expects($this->once())
            ->method('render')
            ->with($this->equalTo('admin/admins/index.html.twig'))
            ->willReturn(new Response());

        $response = $this->adminsController->index();
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testIndexWhenNotAdmin(): void
    {
        $this->adminsController->method('isAdmin')->willReturn(false);
        $this->adminsController->expects($this->once())
            ->method('redirectToRoute')
            ->with($this->equalTo('app_admin_login'))
            ->willReturn(new RedirectResponse('/admin/login'));

        $response = $this->adminsController->index();
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testCreateWhenNotAdmin(): void
    {
        $this->adminsController->method('isAdmin')->willReturn(false);
        $this->adminsController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_login')
            ->willReturn(new RedirectResponse('/admin/login'));

        $response = $this->adminsController->create(new Request());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testCreateGetRequest(): void
    {
        $request = new Request();
        $this->adminsController->method('isAdmin')->willReturn(true);

        $this->adminsController->expects($this->once())
            ->method('render')
            ->with('admin/admins/edit.html.twig')
            ->willReturn(new Response());

        $response = $this->adminsController->create($request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCreatePostRequestStayOnPage(): void
    {
        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->adminsController->method('isAdmin')->willReturn(true);
        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(true);

        $this->adminsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $adminFormTypeMock = $this->createMock(AdminFormType::class);
        $entity = new Admin();
        $entity->setId(1);
        $adminFormTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => []]);

        $this->adminsController->setFormType($adminFormTypeMock);
        $this->adminsController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_admins_index')
            ->willReturn(new RedirectResponse('/admin/admins'));

        $response = $this->adminsController->create($request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testCreatePostRequestNotStayOnPage(): void
    {
        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->adminsController->method('isAdmin')->willReturn(true);
        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(false);

        $this->adminsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $adminFormTypeMock = $this->createMock(AdminFormType::class);
        $entity = new Admin();
        $entity->setId(1);
        $adminFormTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => []]);

        $this->adminsController->setFormType($adminFormTypeMock);
        $this->adminsController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_admins_edit')
            ->willReturn(new RedirectResponse('/admins/edit/1'));

        $response = $this->adminsController->create($request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testCreatePostRequestHasError(): void
    {
        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->adminsController->method('isAdmin')->willReturn(true);

        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(false);
        $this->adminsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $adminFormTypeMock = $this->createMock(AdminFormType::class);
        $entity = new Admin();
        $entity->setId(1);
        $adminFormTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => ["email" => "Not valid"]]);
        $this->adminsController->setFormType($adminFormTypeMock);

        $this->adminsController->expects($this->once())
            ->method('render')
            ->with($this->equalTo('admin/admins/edit.html.twig'))
            ->willReturn(new Response());

        $response = $this->adminsController->create($request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testEditWhenNotAdmin(): void
    {
        $entity = new Admin();
        $entity->setId(1);

        $this->adminsController->method('isAdmin')->willReturn(false);
        $this->adminsController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_login')
            ->willReturn(new RedirectResponse('/admin/login'));

        $response = $this->adminsController->edit($entity->getId(), new Request());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testEditGetRequestNotFindEntity(): void
    {
        $entity = new Admin();
        $entity->setId(1);

        $request = new Request();
        $this->adminsController->method('isAdmin')->willReturn(true);
        $this->adminRepositoryMock->method("find")->willReturn(null);

        $this->adminsController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_admins_index')
            ->willReturn(new RedirectResponse('/admin/admins'));
        $response = $this->adminsController->edit($entity->getId(), $request);
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testEditGetRequestFindEntity(): void
    {
        $entity = new Admin();
        $entity->setId(1);

        $request = new Request();
        $this->adminRepositoryMock->method("find")->willReturn($entity);
        $this->adminsController->method('isAdmin')->willReturn(true);

        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->setExtraParameter("Entity", $entity);
        $this->adminsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $this->adminsController->expects($this->once())
            ->method('render')
            ->with('admin/admins/edit.html.twig')
            ->willReturn(new Response());

        $response = $this->adminsController->edit($entity->getId(), $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testEditPostRequestStayOnPage(): void
    {

        $entity = new Admin();
        $entity->setId(1);

        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->adminsController->method('isAdmin')->willReturn(true);
        $this->adminRepositoryMock->method("find")->willReturn($entity);

        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(true);
        $this->adminsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $adminFormTypeMock = $this->createMock(AdminFormType::class);
        $adminFormTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => []]);
        $this->adminsController->setFormType($adminFormTypeMock);

        $this->adminsController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_admins_index')
            ->willReturn(new RedirectResponse('/admin/admins'));

        $response = $this->adminsController->edit($entity->getId(), $request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testEditPostRequestHasError(): void
    {
        $entity = new Admin();
        $entity->setId(1);

        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->adminsController->method('isAdmin')->willReturn(true);
        $this->adminRepositoryMock->method("find")->willReturn($entity);

        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->setExtraParameter("Entity", $entity);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(false);
        $this->adminsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $adminFormTypeMock = $this->createMock(AdminFormType::class);
        $adminFormTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => ["email" => "Not valid"]]);
        $this->adminsController->setFormType($adminFormTypeMock);

        $this->adminsController->expects($this->once())
            ->method('render')
            ->with('admin/admins/edit.html.twig')
            ->willReturn(new Response());

        $response = $this->adminsController->edit($entity->getId(), $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testEditPostRequestNotStayOnPage(): void
    {
        $entity = new Admin();
        $entity->setId(1);

        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->adminsController->method('isAdmin')->willReturn(true);
        $this->adminRepositoryMock->method("find")->willReturn($entity);

        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->setExtraParameter("Entity", $entity);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(false);
        $this->adminsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $adminFormTypeMock = $this->createMock(AdminFormType::class);
        $adminFormTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => []]);
        $this->adminsController->setFormType($adminFormTypeMock);

        $this->adminsController->expects($this->once())
            ->method('render')
            ->with('admin/admins/edit.html.twig')
            ->willReturn(new Response());

        $response = $this->adminsController->edit($entity->getId(), $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDeleteWhenNotAdmin(): void
    {
        $this->adminsController->method('isAdmin')->willReturn(false);

        $response = $this->adminsController->delete(1, new Request());
        $this->assertEquals('{"error":true}', $response->getContent());
    }

    public function testDeleteInvalidRequest(): void
    {
        $this->adminsController->method('isAdmin')->willReturn(true);
        $request = new Request();

        $response = $this->adminsController->delete(1, $request);
        $this->assertEquals('{"error":true}', $response->getContent());
    }

    public function testDeleteNonExistingEntity(): void
    {
        $this->adminsController->method('isAdmin')->willReturn(true);
        $this->adminRepositoryMock->method('find')->willReturn(null);
        $request = new Request([], ['delete' => 1], [], [], [], ['REQUEST_METHOD' => 'POST']);

        $response = $this->adminsController->delete(1, $request);
        $this->assertEquals('{"error":true}', $response->getContent());
    }

    public function testDeleteSuccessful(): void
    {
        $entity = new Admin();

        $this->adminsController->method('isAdmin')->willReturn(true);
        $this->adminRepositoryMock->method('find')->willReturn($entity);
        $this->entityManagerMock->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($entity));
        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $request = new Request([], ['delete' => 1], [], [], [], ['REQUEST_METHOD' => 'POST']);

        $response = $this->adminsController->delete(1, $request);
        $this->assertEquals('{"success":true}', $response->getContent());
    }
}

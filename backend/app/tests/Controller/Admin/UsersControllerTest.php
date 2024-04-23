<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\AdminsController;
use App\Controller\Admin\DirtyWordsController;
use App\Controller\Admin\UsersController;
use App\Entity\Admin;
use App\Entity\DirtyWord;
use App\Entity\User;
use App\Form\AdminFormType;
use App\Form\DirtyWordFormType;
use App\Form\UserFormType;
use App\Helper\AdminHtmlDetails;
use App\Repository\AdminRepository;
use App\Repository\DirtyWordRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Config\Security\ProviderConfig\Memory\UserConfig;

class UsersControllerTest extends TestCase
{
    private $usersController;
    private $userRepositoryMock;
    private $requestStack;
    private $session;
    private $entityManagerMock;

    protected function setUp(): void
    {
        $this->userRepositoryMock = $this->createMock(UserRepository::class);
        $this->session = new Session(new MockArraySessionStorage());
        $this->requestStack = new RequestStack();
        $request = new Request();
        $request->setSession($this->session);
        $this->requestStack->push($request);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $this->usersController = $this->getMockBuilder(UsersController::class)
            ->setConstructorArgs([$this->userRepositoryMock, $this->requestStack, $this->entityManagerMock])
            ->onlyMethods(['isAdmin', 'render', 'redirectToRoute'])
            ->getMock();
    }

    public function testIndexWhenAdmin(): void
    {
        $queryMock = $this->createMock(Query::class);
        $queryMock->method('getSQL')->willReturn('SELECT something');

        $this->userRepositoryMock->method('adminListing')->willReturn($queryMock);
        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->expects($this->once())
            ->method('setPagerData')
            ->with($this->equalTo($queryMock), $this->equalTo(1), $this->equalTo(25));

        $this->usersController->setAdminHtmlDetails($adminHtmlDetailsMock);
        $this->usersController->method('isAdmin')->willReturn(true);

        $this->usersController->expects($this->once())
            ->method('render')
            ->with($this->equalTo('admin/users/index.html.twig'))
            ->willReturn(new Response());

        $response = $this->usersController->index();
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testIndexWhenNotAdmin(): void
    {
        $this->usersController->method('isAdmin')->willReturn(false);
        $this->usersController->expects($this->once())
            ->method('redirectToRoute')
            ->with($this->equalTo('app_admin_login'))
            ->willReturn(new RedirectResponse('/admin/login'));

        $response = $this->usersController->index();
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testCreateWhenNotAdmin(): void
    {
        $this->usersController->method('isAdmin')->willReturn(false);
        $this->usersController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_login')
            ->willReturn(new RedirectResponse('/admin/login'));

        $response = $this->usersController->create(new Request());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testCreateGetRequest(): void
    {
        $request = new Request();
        $this->usersController->method('isAdmin')->willReturn(true);

        $this->usersController->expects($this->once())
            ->method('render')
            ->with('admin/users/edit.html.twig')
            ->willReturn(new Response());

        $response = $this->usersController->create($request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCreatePostRequestStayOnPage(): void
    {
        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->usersController->method('isAdmin')->willReturn(true);
        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(true);

        $this->usersController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $formTypeMock = $this->createMock(UserFormType::class);
        $entity = new User();
        $entity->setId(1);
        $formTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => []]);

        $this->usersController->setFormType($formTypeMock);
        $this->usersController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_users_index')
            ->willReturn(new RedirectResponse('/admin/users'));

        $response = $this->usersController->create($request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testCreatePostRequestNotStayOnPage(): void
    {
        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->usersController->method('isAdmin')->willReturn(true);
        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(false);

        $this->usersController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $formTypeMock = $this->createMock(UserFormType::class);
        $entity = new User();
        $entity->setId(1);
        $formTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => []]);

        $this->usersController->setFormType($formTypeMock);
        $this->usersController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_users_edit')
            ->willReturn(new RedirectResponse('/admin/users/edit/1'));

        $response = $this->usersController->create($request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testCreatePostRequestHasError(): void
    {
        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->usersController->method('isAdmin')->willReturn(true);

        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(false);
        $this->usersController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $formTypeMock = $this->createMock(UserFormType::class);
        $entity = new User();
        $entity->setId(1);
        $formTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => ["email" => "Not valid"]]);
        $this->usersController->setFormType($formTypeMock);

        $this->usersController->expects($this->once())
            ->method('render')
            ->with($this->equalTo('admin/users/edit.html.twig'))
            ->willReturn(new Response());

        $response = $this->usersController->create($request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testEditWhenNotAdmin(): void
    {
        $entity = new Admin();
        $entity->setId(1);

        $this->usersController->method('isAdmin')->willReturn(false);
        $this->usersController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_login')
            ->willReturn(new RedirectResponse('/admin/login'));

        $response = $this->usersController->edit($entity->getId(), new Request());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testEditGetRequestNotFindEntity(): void
    {
        $entity = new User();
        $entity->setId(1);

        $request = new Request();
        $this->usersController->method('isAdmin')->willReturn(true);
        $this->userRepositoryMock->method("find")->willReturn(null);

        $this->usersController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_users_index')
            ->willReturn(new RedirectResponse('/admin/users'));
        $response = $this->usersController->edit($entity->getId(), $request);
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testEditGetRequestFindEntity(): void
    {
        $entity = new User();
        $entity->setId(1);

        $request = new Request();
        $this->userRepositoryMock->method("find")->willReturn($entity);
        $this->usersController->method('isAdmin')->willReturn(true);

        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->setExtraParameter("Entity", $entity);
        $this->usersController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $this->usersController->expects($this->once())
            ->method('render')
            ->with('admin/users/edit.html.twig')
            ->willReturn(new Response());

        $response = $this->usersController->edit($entity->getId(), $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testEditPostRequestStayOnPage(): void
    {

        $entity = new User();
        $entity->setId(1);

        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->usersController->method('isAdmin')->willReturn(true);
        $this->userRepositoryMock->method("find")->willReturn($entity);

        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(true);
        $this->usersController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $adminFormTypeMock = $this->createMock(UserFormType::class);
        $adminFormTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => []]);
        $this->usersController->setFormType($adminFormTypeMock);

        $this->usersController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_users_index')
            ->willReturn(new RedirectResponse('/admin/users'));

        $response = $this->usersController->edit($entity->getId(), $request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testEditPostRequestHasError(): void
    {
        $entity = new User();
        $entity->setId(1);

        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->usersController->method('isAdmin')->willReturn(true);
        $this->userRepositoryMock->method("find")->willReturn($entity);

        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->setExtraParameter("Entity", $entity);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(false);
        $this->usersController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $formTypeMock = $this->createMock(UserFormType::class);
        $formTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => ["email" => "Not valid"]]);
        $this->usersController->setFormType($formTypeMock);

        $this->usersController->expects($this->once())
            ->method('render')
            ->with('admin/users/edit.html.twig')
            ->willReturn(new Response());

        $response = $this->usersController->edit($entity->getId(), $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testEditPostRequestNotStayOnPage(): void
    {
        $entity = new User();
        $entity->setId(1);

        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->usersController->method('isAdmin')->willReturn(true);
        $this->userRepositoryMock->method("find")->willReturn($entity);

        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->setExtraParameter("Entity", $entity);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(false);
        $this->usersController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $formTypeMock = $this->createMock(UserFormType::class);
        $formTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => []]);
        $this->usersController->setFormType($formTypeMock);

        $this->usersController->expects($this->once())
            ->method('render')
            ->with('admin/users/edit.html.twig')
            ->willReturn(new Response());

        $response = $this->usersController->edit($entity->getId(), $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDeleteWhenNotAdmin(): void
    {
        $this->usersController->method('isAdmin')->willReturn(false);

        $response = $this->usersController->delete(1, new Request());
        $this->assertEquals('{"error":true}', $response->getContent());
    }

    public function testDeleteInvalidRequest(): void
    {
        $this->usersController->method('isAdmin')->willReturn(true);
        $request = new Request();

        $response = $this->usersController->delete(1, $request);
        $this->assertEquals('{"error":true}', $response->getContent());
    }

    public function testDeleteNonExistingEntity(): void
    {
        $this->usersController->method('isAdmin')->willReturn(true);
        $this->userRepositoryMock->method('find')->willReturn(null);
        $request = new Request([], ['delete' => 1], [], [], [], ['REQUEST_METHOD' => 'POST']);

        $response = $this->usersController->delete(1, $request);
        $this->assertEquals('{"error":true}', $response->getContent());
    }

    public function testDeleteSuccessful(): void
    {
        $entity = new User();

        $this->usersController->method('isAdmin')->willReturn(true);
        $this->userRepositoryMock->method('find')->willReturn($entity);
        $this->entityManagerMock->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($entity));
        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $request = new Request([], ['delete' => 1], [], [], [], ['REQUEST_METHOD' => 'POST']);

        $response = $this->usersController->delete(1, $request);
        $this->assertEquals('{"success":true}', $response->getContent());
    }
}

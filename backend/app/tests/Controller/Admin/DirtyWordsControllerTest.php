<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\AdminsController;
use App\Controller\Admin\DirtyWordsController;
use App\Entity\Admin;
use App\Entity\DirtyWord;
use App\Form\AdminFormType;
use App\Form\DirtyWordFormType;
use App\Helper\AdminHtmlDetails;
use App\Repository\AdminRepository;
use App\Repository\DirtyWordRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class DirtyWordsControllerTest extends TestCase
{
    private $dirtyWordsController;
    private $dirtyWordRepositoryMock;
    private $requestStack;
    private $session;
    private $entityManagerMock;

    protected function setUp(): void
    {
        $this->dirtyWordRepositoryMock = $this->createMock(DirtyWordRepository::class);
        $this->session = new Session(new MockArraySessionStorage());
        $this->requestStack = new RequestStack();
        $request = new Request();
        $request->setSession($this->session);
        $this->requestStack->push($request);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $this->dirtyWordsController = $this->getMockBuilder(DirtyWordsController::class)
            ->setConstructorArgs([$this->dirtyWordRepositoryMock, $this->requestStack, $this->entityManagerMock])
            ->onlyMethods(['isAdmin', 'render', 'redirectToRoute'])
            ->getMock();
    }

    public function testIndexWhenAdmin(): void
    {
        $queryMock = $this->createMock(Query::class);
        $queryMock->method('getSQL')->willReturn('SELECT something');

        $this->dirtyWordRepositoryMock->method('adminListing')->willReturn($queryMock);
        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->expects($this->once())
            ->method('setPagerData')
            ->with($this->equalTo($queryMock), $this->equalTo(1), $this->equalTo(25));

        $this->dirtyWordsController->setAdminHtmlDetails($adminHtmlDetailsMock);
        $this->dirtyWordsController->method('isAdmin')->willReturn(true);

        $this->dirtyWordsController->expects($this->once())
            ->method('render')
            ->with($this->equalTo('admin/dirtywords/index.html.twig'))
            ->willReturn(new Response());

        $response = $this->dirtyWordsController->index();
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testIndexWhenNotAdmin(): void
    {
        $this->dirtyWordsController->method('isAdmin')->willReturn(false);
        $this->dirtyWordsController->expects($this->once())
            ->method('redirectToRoute')
            ->with($this->equalTo('app_admin_login'))
            ->willReturn(new RedirectResponse('/admin/login'));

        $response = $this->dirtyWordsController->index();
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testCreateWhenNotAdmin(): void
    {
        $this->dirtyWordsController->method('isAdmin')->willReturn(false);
        $this->dirtyWordsController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_login')
            ->willReturn(new RedirectResponse('/admin/login'));

        $response = $this->dirtyWordsController->create(new Request());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testCreateGetRequest(): void
    {
        $request = new Request();
        $this->dirtyWordsController->method('isAdmin')->willReturn(true);

        $this->dirtyWordsController->expects($this->once())
            ->method('render')
            ->with('admin/dirtywords/edit.html.twig')
            ->willReturn(new Response());

        $response = $this->dirtyWordsController->create($request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCreatePostRequestStayOnPage(): void
    {
        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->dirtyWordsController->method('isAdmin')->willReturn(true);
        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(true);

        $this->dirtyWordsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $formTypeMock = $this->createMock(DirtyWordFormType::class);
        $entity = new DirtyWord();
        $entity->setId(1);
        $formTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => []]);

        $this->dirtyWordsController->setFormType($formTypeMock);
        $this->dirtyWordsController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_dirtywords_index')
            ->willReturn(new RedirectResponse('/admin/admins'));

        $response = $this->dirtyWordsController->create($request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testCreatePostRequestNotStayOnPage(): void
    {
        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->dirtyWordsController->method('isAdmin')->willReturn(true);
        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(false);

        $this->dirtyWordsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $formTypeMock = $this->createMock(DirtyWordFormType::class);
        $entity = new DirtyWord();
        $entity->setId(1);
        $formTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => []]);

        $this->dirtyWordsController->setFormType($formTypeMock);
        $this->dirtyWordsController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_dirtywords_edit')
            ->willReturn(new RedirectResponse('/admin/dirtywords/edit/1'));

        $response = $this->dirtyWordsController->create($request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testCreatePostRequestHasError(): void
    {
        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->dirtyWordsController->method('isAdmin')->willReturn(true);

        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(false);
        $this->dirtyWordsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $formTypeMock = $this->createMock(DirtyWordFormType::class);
        $entity = new DirtyWord();
        $entity->setId(1);
        $formTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => ["email" => "Not valid"]]);
        $this->dirtyWordsController->setFormType($formTypeMock);

        $this->dirtyWordsController->expects($this->once())
            ->method('render')
            ->with($this->equalTo('admin/dirtywords/edit.html.twig'))
            ->willReturn(new Response());

        $response = $this->dirtyWordsController->create($request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testEditWhenNotAdmin(): void
    {
        $entity = new Admin();
        $entity->setId(1);

        $this->dirtyWordsController->method('isAdmin')->willReturn(false);
        $this->dirtyWordsController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_login')
            ->willReturn(new RedirectResponse('/admin/login'));

        $response = $this->dirtyWordsController->edit($entity->getId(), new Request());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testEditGetRequestNotFindEntity(): void
    {
        $entity = new DirtyWord();
        $entity->setId(1);

        $request = new Request();
        $this->dirtyWordsController->method('isAdmin')->willReturn(true);
        $this->dirtyWordRepositoryMock->method("find")->willReturn(null);

        $this->dirtyWordsController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_dirtywords_index')
            ->willReturn(new RedirectResponse('/admin/dirtywords'));
        $response = $this->dirtyWordsController->edit($entity->getId(), $request);
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testEditGetRequestFindEntity(): void
    {
        $entity = new DirtyWord();
        $entity->setId(1);

        $request = new Request();
        $this->dirtyWordRepositoryMock->method("find")->willReturn($entity);
        $this->dirtyWordsController->method('isAdmin')->willReturn(true);

        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->setExtraParameter("Entity", $entity);
        $this->dirtyWordsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $this->dirtyWordsController->expects($this->once())
            ->method('render')
            ->with('admin/dirtywords/edit.html.twig')
            ->willReturn(new Response());

        $response = $this->dirtyWordsController->edit($entity->getId(), $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testEditPostRequestStayOnPage(): void
    {

        $entity = new DirtyWord();
        $entity->setId(1);

        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->dirtyWordsController->method('isAdmin')->willReturn(true);
        $this->dirtyWordRepositoryMock->method("find")->willReturn($entity);

        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(true);
        $this->dirtyWordsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $adminFormTypeMock = $this->createMock(DirtyWordFormType::class);
        $adminFormTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => []]);
        $this->dirtyWordsController->setFormType($adminFormTypeMock);

        $this->dirtyWordsController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_dirtywords_index')
            ->willReturn(new RedirectResponse('/admin/dirtywords'));

        $response = $this->dirtyWordsController->edit($entity->getId(), $request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testEditPostRequestHasError(): void
    {
        $entity = new DirtyWord();
        $entity->setId(1);

        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->dirtyWordsController->method('isAdmin')->willReturn(true);
        $this->dirtyWordRepositoryMock->method("find")->willReturn($entity);

        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->setExtraParameter("Entity", $entity);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(false);
        $this->dirtyWordsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $formTypeMock = $this->createMock(DirtyWordFormType::class);
        $formTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => ["email" => "Not valid"]]);
        $this->dirtyWordsController->setFormType($formTypeMock);

        $this->dirtyWordsController->expects($this->once())
            ->method('render')
            ->with('admin/dirtywords/edit.html.twig')
            ->willReturn(new Response());

        $response = $this->dirtyWordsController->edit($entity->getId(), $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testEditPostRequestNotStayOnPage(): void
    {
        $entity = new DirtyWord();
        $entity->setId(1);

        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->dirtyWordsController->method('isAdmin')->willReturn(true);
        $this->dirtyWordRepositoryMock->method("find")->willReturn($entity);

        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->setExtraParameter("Entity", $entity);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(false);
        $this->dirtyWordsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $formTypeMock = $this->createMock(DirtyWordFormType::class);
        $formTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => []]);
        $this->dirtyWordsController->setFormType($formTypeMock);

        $this->dirtyWordsController->expects($this->once())
            ->method('render')
            ->with('admin/dirtywords/edit.html.twig')
            ->willReturn(new Response());

        $response = $this->dirtyWordsController->edit($entity->getId(), $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDeleteWhenNotAdmin(): void
    {
        $this->dirtyWordsController->method('isAdmin')->willReturn(false);

        $response = $this->dirtyWordsController->delete(1, new Request());
        $this->assertEquals('{"error":true}', $response->getContent());
    }

    public function testDeleteInvalidRequest(): void
    {
        $this->dirtyWordsController->method('isAdmin')->willReturn(true);
        $request = new Request();

        $response = $this->dirtyWordsController->delete(1, $request);
        $this->assertEquals('{"error":true}', $response->getContent());
    }

    public function testDeleteNonExistingEntity(): void
    {
        $this->dirtyWordsController->method('isAdmin')->willReturn(true);
        $this->dirtyWordRepositoryMock->method('find')->willReturn(null);
        $request = new Request([], ['delete' => 1], [], [], [], ['REQUEST_METHOD' => 'POST']);

        $response = $this->dirtyWordsController->delete(1, $request);
        $this->assertEquals('{"error":true}', $response->getContent());
    }

    public function testDeleteSuccessful(): void
    {
        $entity = new DirtyWord();

        $this->dirtyWordsController->method('isAdmin')->willReturn(true);
        $this->dirtyWordRepositoryMock->method('find')->willReturn($entity);
        $this->entityManagerMock->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($entity));
        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $request = new Request([], ['delete' => 1], [], [], [], ['REQUEST_METHOD' => 'POST']);

        $response = $this->dirtyWordsController->delete(1, $request);
        $this->assertEquals('{"success":true}', $response->getContent());
    }
}

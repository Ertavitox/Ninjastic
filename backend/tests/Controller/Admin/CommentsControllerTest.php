<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\CommentsController;
use App\Entity\Comment;
use App\Entity\DirtyWord;
use App\Entity\Topic;
use App\Entity\User;
use App\Form\CommentFormType;
use App\Helper\AdminHtmlDetails;
use App\Repository\CommentRepository;
use App\Repository\DirtyWordRepository;
use App\Service\WordCensor;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class CommentsControllerTest extends TestCase
{
    private $commentsController;
    private $commentRepositoryMock;
    private $requestStack;
    private $session;
    private $entityManagerMock;

    protected function setUp(): void
    {
        $this->commentRepositoryMock = $this->createMock(CommentRepository::class);
        $this->session = new Session(new MockArraySessionStorage());
        $this->requestStack = new RequestStack();
        $request = new Request();
        $request->setSession($this->session);
        $this->requestStack->push($request);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $dirtyWordMock = $this->createMock(DirtyWordRepository::class);
        $this->entityManagerMock->method('getRepository')->willReturn($dirtyWordMock);

        $this->commentsController = $this->getMockBuilder(CommentsController::class)
            ->setConstructorArgs([$this->commentRepositoryMock, $this->requestStack, $this->entityManagerMock])
            ->onlyMethods(['isAdmin', 'render', 'redirectToRoute'])
            ->getMock();
    }

    public function testIndexWhenAdmin(): void
    {
        $queryMock = $this->createMock(Query::class);
        $queryMock->method('getSQL')->willReturn('SELECT something');

        $this->commentRepositoryMock->method('adminListing')->willReturn($queryMock);
        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->expects($this->once())
            ->method('setPagerData')
            ->with($this->equalTo($queryMock), $this->equalTo(1), $this->equalTo(25));

        $this->commentsController->setAdminHtmlDetails($adminHtmlDetailsMock);
        $this->commentsController->method('isAdmin')->willReturn(true);

        $this->commentsController->expects($this->once())
            ->method('render')
            ->with($this->equalTo('admin/comments/index.html.twig'))
            ->willReturn(new Response());

        $response = $this->commentsController->index();
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testIndexWhenNotAdmin(): void
    {
        $this->commentsController->method('isAdmin')->willReturn(false);
        $this->commentsController->expects($this->once())
            ->method('redirectToRoute')
            ->with($this->equalTo('app_admin_login'))
            ->willReturn(new RedirectResponse('/admin/login'));

        $response = $this->commentsController->index();
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testCreateWhenNotAdmin(): void
    {
        $this->commentsController->method('isAdmin')->willReturn(false);
        $this->commentsController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_login')
            ->willReturn(new RedirectResponse('/admin/login'));

        $response = $this->commentsController->create(new Request());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testCreateGetRequest(): void
    {
        $request = new Request();
        $this->commentsController->method('isAdmin')->willReturn(true);

        $this->commentsController->expects($this->once())
            ->method('render')
            ->with('admin/comments/edit.html.twig')
            ->willReturn(new Response());

        $response = $this->commentsController->create($request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCreatePostRequestStayOnPage(): void
    {
        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->commentsController->method('isAdmin')->willReturn(true);
        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(true);

        $this->commentsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $dirtyWordMock = $this->createMock(DirtyWordRepository::class);
        $this->entityManagerMock->method('getRepository')->willReturn($dirtyWordMock);

        $wordCensor = $this->createMock(WordCensor::class);
        $wordCensor->method('censorWords')->willReturn("test content");
        $entity = new Comment();
        $entity->setId(1);
        $formTypeMock = $this->createMock(CommentFormType::class);
        $formTypeMock->expects($this->once())->method("createUpdate")->willReturn(["entity" => $entity, "error" => []]);

        $this->commentsController->setFormType($formTypeMock);
        $this->commentsController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_comments_index')
            ->willReturn(new RedirectResponse('/admin/comments'));

        $response = $this->commentsController->create($request);
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testCreatePostRequestNotStayOnPage(): void
    {
        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->commentsController->method('isAdmin')->willReturn(true);
        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(false);

        $this->commentsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $formTypeMock = $this->createMock(CommentFormType::class);
        $entity = new Comment();
        $entity->setId(1);
        $formTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => []]);

        $this->commentsController->setFormType($formTypeMock);
        $this->commentsController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_comments_edit')
            ->willReturn(new RedirectResponse('/comments/edit/1'));

        $response = $this->commentsController->create($request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testCreatePostRequestHasError(): void
    {
        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->commentsController->method('isAdmin')->willReturn(true);

        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(false);
        $this->commentsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $formTypeMock = $this->createMock(CommentFormType::class);
        $entity = new Comment();
        $entity->setId(1);
        $formTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => ["email" => "Not valid"]]);
        $this->commentsController->setFormType($formTypeMock);

        $this->commentsController->expects($this->once())
            ->method('render')
            ->with($this->equalTo('admin/comments/edit.html.twig'))
            ->willReturn(new Response());

        $response = $this->commentsController->create($request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testEditWhenNotAdmin(): void
    {
        $entity = new Comment();
        $entity->setId(1);

        $this->commentsController->method('isAdmin')->willReturn(false);
        $this->commentsController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_login')
            ->willReturn(new RedirectResponse('/admin/login'));

        $response = $this->commentsController->edit($entity->getId(), new Request());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testEditGetRequestNotFindEntity(): void
    {
        $entity = new Comment();
        $entity->setId(1);

        $request = new Request();
        $this->commentsController->method('isAdmin')->willReturn(true);
        $this->commentRepositoryMock->method("find")->willReturn(null);

        $this->commentsController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_comments_index')
            ->willReturn(new RedirectResponse('/admin/comments'));
        $response = $this->commentsController->edit($entity->getId(), $request);
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testEditGetRequestFindEntity(): void
    {
        $entity = new Comment();
        $entity->setId(1);

        $request = new Request();
        $this->commentRepositoryMock->method("find")->willReturn($entity);
        $this->commentsController->method('isAdmin')->willReturn(true);

        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->setExtraParameter("Entity", $entity);
        $this->commentsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $this->commentsController->expects($this->once())
            ->method('render')
            ->with('admin/comments/edit.html.twig')
            ->willReturn(new Response());

        $response = $this->commentsController->edit($entity->getId(), $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testEditPostRequestStayOnPage(): void
    {

        $entity = new Comment();
        $entity->setId(1);

        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->commentsController->method('isAdmin')->willReturn(true);
        $this->commentRepositoryMock->method("find")->willReturn($entity);

        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(true);
        $this->commentsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $formTypeMock = $this->createMock(CommentFormType::class);
        $formTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => []]);
        $this->commentsController->setFormType($formTypeMock);

        $this->commentsController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_comments_index')
            ->willReturn(new RedirectResponse('/admin/comments'));

        $response = $this->commentsController->edit($entity->getId(), $request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testEditPostRequestHasError(): void
    {
        $entity = new Comment();
        $entity->setId(1);

        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->commentsController->method('isAdmin')->willReturn(true);
        $this->commentRepositoryMock->method("find")->willReturn($entity);

        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->setExtraParameter("Entity", $entity);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(false);
        $this->commentsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $formTypeMock = $this->createMock(CommentFormType::class);
        $formTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => ["email" => "Not valid"]]);
        $this->commentsController->setFormType($formTypeMock);

        $this->commentsController->expects($this->once())
            ->method('render')
            ->with('admin/comments/edit.html.twig')
            ->willReturn(new Response());

        $response = $this->commentsController->edit($entity->getId(), $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testEditPostRequestNotStayOnPage(): void
    {
        $entity = new Comment();
        $entity->setId(1);

        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->commentsController->method('isAdmin')->willReturn(true);
        $this->commentRepositoryMock->method("find")->willReturn($entity);

        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->setExtraParameter("Entity", $entity);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(false);
        $this->commentsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $formTypeMock = $this->createMock(CommentFormType::class);
        $formTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => []]);
        $this->commentsController->setFormType($formTypeMock);

        $this->commentsController->expects($this->once())
            ->method('render')
            ->with('admin/comments/edit.html.twig')
            ->willReturn(new Response());

        $response = $this->commentsController->edit($entity->getId(), $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDeleteWhenNotAdmin(): void
    {
        $this->commentsController->method('isAdmin')->willReturn(false);

        $response = $this->commentsController->delete(1, new Request());
        $this->assertEquals('{"error":true}', $response->getContent());
    }

    public function testDeleteInvalidRequest(): void
    {
        $this->commentsController->method('isAdmin')->willReturn(true);
        $request = new Request();

        $response = $this->commentsController->delete(1, $request);
        $this->assertEquals('{"error":true}', $response->getContent());
    }

    public function testDeleteNonExistingEntity(): void
    {
        $this->commentsController->method('isAdmin')->willReturn(true);
        $this->commentRepositoryMock->method('find')->willReturn(null);
        $request = new Request([], ['delete' => 1], [], [], [], ['REQUEST_METHOD' => 'POST']);

        $response = $this->commentsController->delete(1, $request);
        $this->assertEquals('{"error":true}', $response->getContent());
    }

    public function testDeleteSuccessful(): void
    {
        $entity = new Comment();

        $this->commentsController->method('isAdmin')->willReturn(true);
        $this->commentRepositoryMock->method('find')->willReturn($entity);
        $this->entityManagerMock->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($entity));
        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $request = new Request([], ['delete' => 1], [], [], [], ['REQUEST_METHOD' => 'POST']);

        $response = $this->commentsController->delete(1, $request);
        $this->assertEquals('{"success":true}', $response->getContent());
    }
}

<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\AdminsController;
use App\Controller\Admin\DirtyWordsController;
use App\Controller\Admin\TopicsController;
use App\Entity\Admin;
use App\Entity\DirtyWord;
use App\Entity\Topic;
use App\Form\AdminFormType;
use App\Form\DirtyWordFormType;
use App\Form\TopicFormType;
use App\Helper\AdminHtmlDetails;
use App\Repository\AdminRepository;
use App\Repository\DirtyWordRepository;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class TopicsControllerTest extends TestCase
{
    private $topicsController;
    private $topicRepositoryMock;
    private $requestStack;
    private $session;
    private $entityManagerMock;

    protected function setUp(): void
    {
        $this->topicRepositoryMock = $this->createMock(TopicRepository::class);
        $this->session = new Session(new MockArraySessionStorage());
        $this->requestStack = new RequestStack();
        $request = new Request();
        $request->setSession($this->session);
        $this->requestStack->push($request);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $this->topicsController = $this->getMockBuilder(TopicsController::class)
            ->setConstructorArgs([$this->topicRepositoryMock, $this->requestStack, $this->entityManagerMock])
            ->onlyMethods(['isAdmin', 'render', 'redirectToRoute'])
            ->getMock();
    }

    public function testIndexWhenAdmin(): void
    {
        $queryMock = $this->createMock(Query::class);
        $queryMock->method('getSQL')->willReturn('SELECT something');

        $this->topicRepositoryMock->method('adminListing')->willReturn($queryMock);
        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->expects($this->once())
            ->method('setPagerData')
            ->with($this->equalTo($queryMock), $this->equalTo(1), $this->equalTo(25));

        $this->topicsController->setAdminHtmlDetails($adminHtmlDetailsMock);
        $this->topicsController->method('isAdmin')->willReturn(true);

        $this->topicsController->expects($this->once())
            ->method('render')
            ->with($this->equalTo('admin/topics/index.html.twig'))
            ->willReturn(new Response());

        $response = $this->topicsController->index();
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testIndexWhenNotAdmin(): void
    {
        $this->topicsController->method('isAdmin')->willReturn(false);
        $this->topicsController->expects($this->once())
            ->method('redirectToRoute')
            ->with($this->equalTo('app_admin_login'))
            ->willReturn(new RedirectResponse('/admin/login'));

        $response = $this->topicsController->index();
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testCreateWhenNotAdmin(): void
    {
        $this->topicsController->method('isAdmin')->willReturn(false);
        $this->topicsController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_login')
            ->willReturn(new RedirectResponse('/admin/login'));

        $response = $this->topicsController->create(new Request());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testCreateGetRequest(): void
    {
        $request = new Request();
        $this->topicsController->method('isAdmin')->willReturn(true);

        $this->topicsController->expects($this->once())
            ->method('render')
            ->with('admin/topics/edit.html.twig')
            ->willReturn(new Response());

        $response = $this->topicsController->create($request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCreatePostRequestStayOnPage(): void
    {
        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->topicsController->method('isAdmin')->willReturn(true);
        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(true);

        $this->topicsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $formTypeMock = $this->createMock(TopicFormType::class);
        $entity = new Topic();
        $entity->setId(1);
        $formTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => []]);

        $this->topicsController->setFormType($formTypeMock);
        $this->topicsController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_topics_index')
            ->willReturn(new RedirectResponse('/admin/topics'));

        $response = $this->topicsController->create($request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testCreatePostRequestNotStayOnPage(): void
    {
        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->topicsController->method('isAdmin')->willReturn(true);
        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(false);

        $this->topicsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $formTypeMock = $this->createMock(TopicFormType::class);
        $entity = new Topic();
        $entity->setId(1);
        $formTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => []]);

        $this->topicsController->setFormType($formTypeMock);
        $this->topicsController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_topics_edit')
            ->willReturn(new RedirectResponse('/admin/topics/edit/1'));

        $response = $this->topicsController->create($request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testCreatePostRequestHasError(): void
    {
        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->topicsController->method('isAdmin')->willReturn(true);

        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(false);
        $this->topicsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $formTypeMock = $this->createMock(TopicFormType::class);
        $entity = new Topic();
        $entity->setId(1);
        $formTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => ["email" => "Not valid"]]);
        $this->topicsController->setFormType($formTypeMock);

        $this->topicsController->expects($this->once())
            ->method('render')
            ->with($this->equalTo('admin/topics/edit.html.twig'))
            ->willReturn(new Response());

        $response = $this->topicsController->create($request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testEditWhenNotAdmin(): void
    {
        $entity = new Admin();
        $entity->setId(1);

        $this->topicsController->method('isAdmin')->willReturn(false);
        $this->topicsController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_login')
            ->willReturn(new RedirectResponse('/admin/login'));

        $response = $this->topicsController->edit($entity->getId(), new Request());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testEditGetRequestNotFindEntity(): void
    {
        $entity = new Topic();
        $entity->setId(1);

        $request = new Request();
        $this->topicsController->method('isAdmin')->willReturn(true);
        $this->topicRepositoryMock->method("find")->willReturn(null);

        $this->topicsController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_topics_index')
            ->willReturn(new RedirectResponse('/admin/topics'));
        $response = $this->topicsController->edit($entity->getId(), $request);
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testEditGetRequestFindEntity(): void
    {
        $entity = new Topic();
        $entity->setId(1);

        $request = new Request();
        $this->topicRepositoryMock->method("find")->willReturn($entity);
        $this->topicsController->method('isAdmin')->willReturn(true);

        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->setExtraParameter("Entity", $entity);
        $this->topicsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $this->topicsController->expects($this->once())
            ->method('render')
            ->with('admin/topics/edit.html.twig')
            ->willReturn(new Response());

        $response = $this->topicsController->edit($entity->getId(), $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testEditPostRequestStayOnPage(): void
    {

        $entity = new Topic();
        $entity->setId(1);

        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->topicsController->method('isAdmin')->willReturn(true);
        $this->topicRepositoryMock->method("find")->willReturn($entity);

        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(true);
        $this->topicsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $adminFormTypeMock = $this->createMock(TopicFormType::class);
        $adminFormTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => []]);
        $this->topicsController->setFormType($adminFormTypeMock);

        $this->topicsController->expects($this->once())
            ->method('redirectToRoute')
            ->with('app_admin_topics_index')
            ->willReturn(new RedirectResponse('/admin/topics'));

        $response = $this->topicsController->edit($entity->getId(), $request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testEditPostRequestHasError(): void
    {
        $entity = new Topic();
        $entity->setId(1);

        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->topicsController->method('isAdmin')->willReturn(true);
        $this->topicRepositoryMock->method("find")->willReturn($entity);

        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->setExtraParameter("Entity", $entity);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(false);
        $this->topicsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $formTypeMock = $this->createMock(TopicFormType::class);
        $formTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => ["email" => "Not valid"]]);
        $this->topicsController->setFormType($formTypeMock);

        $this->topicsController->expects($this->once())
            ->method('render')
            ->with('admin/topics/edit.html.twig')
            ->willReturn(new Response());

        $response = $this->topicsController->edit($entity->getId(), $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testEditPostRequestNotStayOnPage(): void
    {
        $entity = new Topic();
        $entity->setId(1);

        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $this->topicsController->method('isAdmin')->willReturn(true);
        $this->topicRepositoryMock->method("find")->willReturn($entity);

        $adminHtmlDetailsMock = $this->createMock(AdminHtmlDetails::class);
        $adminHtmlDetailsMock->setExtraParameter("Entity", $entity);
        $adminHtmlDetailsMock->method('checkStayPage')->willReturn(false);
        $this->topicsController->setAdminHtmlDetails($adminHtmlDetailsMock);

        $formTypeMock = $this->createMock(TopicFormType::class);
        $formTypeMock->method("createUpdate")->willReturn(["entity" => $entity, "error" => []]);
        $this->topicsController->setFormType($formTypeMock);

        $this->topicsController->expects($this->once())
            ->method('render')
            ->with('admin/topics/edit.html.twig')
            ->willReturn(new Response());

        $response = $this->topicsController->edit($entity->getId(), $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDeleteWhenNotAdmin(): void
    {
        $this->topicsController->method('isAdmin')->willReturn(false);

        $response = $this->topicsController->delete(1, new Request());
        $this->assertEquals('{"error":true}', $response->getContent());
    }

    public function testDeleteInvalidRequest(): void
    {
        $this->topicsController->method('isAdmin')->willReturn(true);
        $request = new Request();

        $response = $this->topicsController->delete(1, $request);
        $this->assertEquals('{"error":true}', $response->getContent());
    }

    public function testDeleteNonExistingEntity(): void
    {
        $this->topicsController->method('isAdmin')->willReturn(true);
        $this->topicRepositoryMock->method('find')->willReturn(null);
        $request = new Request([], ['delete' => 1], [], [], [], ['REQUEST_METHOD' => 'POST']);

        $response = $this->topicsController->delete(1, $request);
        $this->assertEquals('{"error":true}', $response->getContent());
    }

    public function testDeleteSuccessful(): void
    {
        $entity = new Topic();

        $this->topicsController->method('isAdmin')->willReturn(true);
        $this->topicRepositoryMock->method('find')->willReturn($entity);
        $this->entityManagerMock->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($entity));
        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $request = new Request([], ['delete' => 1], [], [], [], ['REQUEST_METHOD' => 'POST']);

        $response = $this->topicsController->delete(1, $request);
        $this->assertEquals('{"success":true}', $response->getContent());
    }
}

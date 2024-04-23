<?php

use App\Helper\AdminHtmlDetails;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;



class AdminHtmlDetailsTest extends TestCase
{
    private AdminHtmlDetails $adminHtmlDetails;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->adminHtmlDetails = new AdminHtmlDetails('TestController');
    }

    public function testSetDefaultAndExtraParameter(): void
    {
        $this->adminHtmlDetails->setDefault('TestAction', '/test-url', 'Test Page Title', []);
        $this->adminHtmlDetails->setExtraParameter('extraKey', 'extraValue');

        $data = $this->adminHtmlDetails->getData();
        $this->assertEquals('TestController', $data['controller_name']);
        $this->assertEquals('TestAction', $data['action_name']);
        $this->assertEquals('/test-url', $data['controller_url']);
        $this->assertEquals('Test Page Title', $data['page_title']);
        $this->assertEquals([], $data['error']);
        $this->assertEquals('extraValue', $data['extraKey']);
    }

    public function testCheckStayPage(): void
    {
        $_POST['stay'] = 1;
        $this->assertFalse($this->adminHtmlDetails->checkStayPage());

        unset($_POST['stay']);
        $this->assertTrue($this->adminHtmlDetails->checkStayPage());
    }

    protected function tearDown(): void
    {
        $_POST = [];
    }
}

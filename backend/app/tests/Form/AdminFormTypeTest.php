<?php

use App\Entity\Admin;
use App\Form\AdminFormType;
use App\Tests\Form\MockDriverException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use PHPUnit\Framework\TestCase;

class AdminFormTypeTest extends TestCase
{
    private AdminFormType $adminForm;
    private $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->adminForm = new AdminFormType();
    }

    public function testCreateUpdateSuccess(): void
    {
        $_POST = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_again' => 'password123',
            'status' => 1
        ];

        $admin = new Admin();

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($admin));

        $this->entityManager->expects($this->once())
            ->method('flush');

        $result = $this->adminForm->createUpdate($this->entityManager, $admin);

        $this->assertArrayHasKey('entity', $result);
        $this->assertArrayHasKey('error', $result);
        $this->assertEmpty($result['error']);
        $this->assertInstanceOf(Admin::class, $result['entity']);
        $this->assertEquals('John Doe', $result['entity']->getName());
        $this->assertEquals('john@example.com', $result['entity']->getEmail());
    }

    public function testCreateUpdateFailureUniqueEmail(): void
    {
        $_POST = [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_again' => 'password123',
            'status' => 1
        ];

        $admin = new Admin();

        $mockedException = new MockDriverException("SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry");
        $mockedUniqueConstraintViolationException = new UniqueConstraintViolationException(
            $mockedException,
            null
        );

        $this->entityManager->method('persist')
            ->will($this->throwException($mockedUniqueConstraintViolationException));

        $result = $this->adminForm->createUpdate($this->entityManager, $admin);
        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('email', $result['error']);
    }

    protected function tearDown(): void
    {
        $_POST = [];
    }
}

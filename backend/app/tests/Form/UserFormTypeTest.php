<?php

use App\Entity\User;
use App\Form\UserFormType;
use App\Tests\Form\MockDriverException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use PHPUnit\Framework\TestCase;

class UserFormTypeTest extends TestCase
{
    private $entityManager;
    private $userFormType;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->userFormType = new UserFormType();
    }

    public function testCreateUpdateSuccess(): void
    {
        $_POST = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'securepassword123',
            'password_again' => 'securepassword123',
            'status' => 1
        ];

        $user = new User();

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($user));

        $this->entityManager->expects($this->once())
            ->method('flush');

        $result = $this->userFormType->createUpdate($this->entityManager, $user);

        $this->assertArrayHasKey('entity', $result);
        $this->assertArrayHasKey('error', $result);
        $this->assertEmpty($result['error']);
        $this->assertInstanceOf(User::class, $result['entity']);
        $this->assertEquals('John Doe', $result['entity']->getName());
        $this->assertEquals('john@example.com', $result['entity']->getEmail());
    }

    public function testCreateUpdateWithDuplicateEmail(): void
    {
        $_POST = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'securepassword123',
            'password_again' => 'securepassword123',
            'status' => 1
        ];

        $user = new User();

        $mockedException = new MockDriverException("SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry");
        $mockedUniqueConstraintViolationException = new UniqueConstraintViolationException(
            $mockedException,
            null
        );
        $this->entityManager->method('persist')
            ->will($this->throwException($mockedUniqueConstraintViolationException));

        $result = $this->userFormType->createUpdate($this->entityManager, $user);

        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('email', $result['error']);
    }

    protected function tearDown(): void
    {
        $_POST = [];
    }
}

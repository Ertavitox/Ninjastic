<?php

use App\Entity\Admin;
use PHPUnit\Framework\TestCase;

class AdminTest extends TestCase
{
    private Admin $admin;

    protected function setUp(): void
    {
        $this->admin = new Admin();
    }

    public function testSetAndGetId(): void
    {
        $id = 1;
        $this->admin->setId($id);
        $this->assertEquals($id, $this->admin->getId());
    }

    public function testSetAndGetName(): void
    {
        $name = 'Admin Name';
        $this->admin->setName($name);
        $this->assertEquals($name, $this->admin->getName());
    }

    public function testSetAndGetEmail(): void
    {
        $email = 'admin@example.com';
        $this->admin->setEmail($email);
        $this->assertEquals($email, $this->admin->getEmail());
    }

    public function testSetAndGetStatus(): void
    {
        $status = Admin::STATUS_INACTIVE;
        $this->admin->setStatus($status);
        $this->assertEquals($status, $this->admin->getStatus());
    }

    public function testShowStatusText(): void
    {
        $this->admin->setStatus(Admin::STATUS_ACTIVE);
        $this->assertEquals('Active', $this->admin->showStatusText());

        $this->admin->setStatus(Admin::STATUS_INACTIVE);
        $this->assertEquals('Inactive', $this->admin->showStatusText());
    }

    public function testInvalidStatusException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->admin->setStatus(3); // Invalid status
    }

    public function testSetAndGetCreatedAt(): void
    {
        $date = new \DateTimeImmutable();
        $this->admin->setCreatedAt($date);
        $this->assertEquals($date, $this->admin->getCreatedAt());
    }

    public function testSetAndGetUpdatedAt(): void
    {
        $date = new \DateTimeImmutable();
        $this->admin->setUpdatedAt($date);
        $this->assertEquals($date, $this->admin->getUpdatedAt());
    }

    public function testSetAndGetPassword(): void
    {
        $password = 'password123';
        $this->admin->setPassword($password, true);
        $this->assertEquals($password, $this->admin->getPassword());
    }

    public function testPasswordHash(): void
    {
        $password = 'password123';
        $hashedPassword = Admin::generatePassHash($password);
        $this->assertNotEquals($password, $hashedPassword);
    }
}

<?php

use App\Entity\User;
use App\Entity\Topic;
use App\Entity\Comment;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testSetAndGetId(): void
    {
        $id = 1;
        $this->user->setId($id);
        $this->assertEquals($id, $this->user->getId());
    }

    public function testSetAndGetName(): void
    {
        $name = 'John Doe';
        $this->user->setName($name);
        $this->assertEquals($name, $this->user->getName());
    }

    public function testSetAndGetEmail(): void
    {
        $email = 'john@example.com';
        $this->user->setEmail($email);
        $this->assertEquals($email, $this->user->getEmail());
    }

    public function testSetAndGetPassword(): void
    {
        $password = 'password123';
        $this->user->setPassword($password);
        $this->assertNotEquals($password, $this->user->getPassword()); // Should be hashed
    }

    public function testShowStatusText(): void
    {
        $this->user->setStatus(User::STATUS_ACTIVE);
        $this->assertEquals('Active', $this->user->showStatusText());

        $this->user->setStatus(User::STATUS_INACTIVE);
        $this->assertEquals('Inactive', $this->user->showStatusText());
    }

    public function testSetAndGetCreatedAt(): void
    {
        $date = new DateTimeImmutable();
        $this->user->setCreatedAt($date);
        $this->assertSame($date, $this->user->getCreatedAt());
    }

    public function testSetAndGetUpdatedAt(): void
    {
        $date = new DateTimeImmutable();
        $this->user->setUpdatedAt($date);
        $this->assertSame($date, $this->user->getUpdatedAt());
    }

    public function testAddAndRemoveTopic(): void
    {
        $topic = new Topic();

        $this->user->addTopic($topic);
        $this->assertCount(1, $this->user->getTopics());

        $this->user->removeTopic($topic);
        $this->assertCount(0, $this->user->getTopics());
    }

    public function testAddAndRemoveComment(): void
    {
        $comment = new Comment();

        $this->user->addComment($comment);
        $this->assertCount(1, $this->user->getComments());

        $this->user->removeComment($comment);
        $this->assertCount(0, $this->user->getComments());
    }

    public function testSetAndGetRoles(): void
    {
        $roles = ['ROLE_USER', 'ROLE_ADMIN'];
        $this->user->setRoles($roles);
        $this->assertEquals($roles, $this->user->getRoles());
    }
}

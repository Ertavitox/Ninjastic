<?php

use App\Entity\Topic;
use App\Entity\User;
use App\Entity\Comment;
use PHPUnit\Framework\TestCase;

class TopicTest extends TestCase
{
    private Topic $topic;

    protected function setUp(): void
    {
        $this->topic = new Topic();
    }

    public function testSetAndGetId(): void
    {
        $id = 1;
        $this->topic->setId($id);
        $this->assertEquals($id, $this->topic->getId());
    }

    public function testSetAndGetUser(): void
    {
        $user = new User();
        $this->topic->setUser($user);
        $this->assertSame($user, $this->topic->getUser());
    }

    public function testSetAndGetName(): void
    {
        $name = 'Test Topic';
        $this->topic->setName($name);
        $this->assertEquals($name, $this->topic->getName());
    }

    public function testSetAndGetDescription(): void
    {
        $description = 'Test Description';
        $this->topic->setDescription($description);
        $this->assertEquals($description, $this->topic->getDescription());
    }

    public function testShowStatusText(): void
    {
        $this->topic->setStatus(Topic::STATUS_ACTIVE);
        $this->assertEquals('Active', $this->topic->showStatusText());

        $this->topic->setStatus(Topic::STATUS_INACTIVE);
        $this->assertEquals('Inactive', $this->topic->showStatusText());
    }

    public function testSetAndGetCreatedAt(): void
    {
        $date = new DateTimeImmutable();
        $this->topic->setCreatedAt($date);
        $this->assertSame($date, $this->topic->getCreatedAt());
    }

    public function testSetAndGetUpdatedAt(): void
    {
        $date = new DateTimeImmutable();
        $this->topic->setUpdatedAt($date);
        $this->assertSame($date, $this->topic->getUpdatedAt());
    }

    public function testAddAndRemoveComment(): void
    {
        $comment = new Comment();

        $this->topic->addComment($comment);
        $this->assertCount(1, $this->topic->getComments());

        $this->topic->removeComment($comment);
        $this->assertCount(0, $this->topic->getComments());
    }
}

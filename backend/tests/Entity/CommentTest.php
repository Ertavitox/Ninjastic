<?php

use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Topic;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    private Comment $comment;

    protected function setUp(): void
    {
        $this->comment = new Comment();
    }

    public function testSetAndGetId(): void
    {
        $id = 1;
        $this->comment->setId($id);
        $this->assertEquals($id, $this->comment->getId());
    }

    public function testSetAndGetUser(): void
    {
        $user = new User();
        $this->comment->setUser($user);
        $this->assertSame($user, $this->comment->getUser());
    }

    public function testSetAndGetTopic(): void
    {
        $topic = new Topic();
        $this->comment->setTopic($topic);
        $this->assertSame($topic, $this->comment->getTopic());
    }

    public function testSetAndgetMessage(): void
    {
        $message = "Test message";
        $this->comment->setMessage($message);
        $this->assertEquals($message, $this->comment->getMessage());
    }

    public function testSetAndGetOriginal(): void
    {
        $original = "Original message";
        $this->comment->setOriginal($original);
        $this->assertEquals($original, $this->comment->getOriginal());
    }

    public function testSetAndGetStatus(): void
    {
        $this->comment->setStatus(Comment::STATUS_INACTIVE);
        $this->assertEquals(Comment::STATUS_INACTIVE, $this->comment->getStatus());
    }

    public function testShowStatusText(): void
    {
        $this->comment->setStatus(Comment::STATUS_ACTIVE);
        $this->assertEquals("Active", $this->comment->showStatusText());

        $this->comment->setStatus(Comment::STATUS_INACTIVE);
        $this->assertEquals("Inactive", $this->comment->showStatusText());
    }

    public function testInvalidStatusException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->comment->setStatus(3); // Invalid status
    }

    public function testSetAndGetCreatedAt(): void
    {
        $date = new DateTimeImmutable();
        $this->comment->setCreatedAt($date);
        $this->assertSame($date, $this->comment->getCreatedAt());
    }

    public function testSetAndGetUpdatedAt(): void
    {
        $date = new DateTimeImmutable();
        $this->comment->setUpdatedAt($date);
        $this->assertSame($date, $this->comment->getUpdatedAt());
    }
}

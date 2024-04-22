<?php


use App\Entity\Comment;
use App\Entity\DirtyWord;
use App\Entity\Topic;
use App\Entity\User;
use App\Form\CommentFormType;
use App\Repository\DirtyWordRepository;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use App\Service\WordCensor;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class CommentFormTypeTest extends TestCase
{
    private $entityManager;
    private $wordCensor;
    private $commentFormType;
    private $topicRepository;
    private $userRepository;
    private $dirtyWordRepository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->topicRepository = $this->createMock(TopicRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->dirtyWordRepository = $this->createMock(DirtyWordRepository::class);

        $this->entityManager
            ->method('getRepository')
            ->willReturnCallback(function ($entityClass) {
                switch ($entityClass) {
                    case Topic::class:
                        return $this->topicRepository;
                    case User::class:
                        return $this->userRepository;
                    case DirtyWord::class:
                        return $this->dirtyWordRepository;
                    default:
                        return null;
                }
            });

        $this->wordCensor = new WordCensor($this->entityManager);
        $this->commentFormType = new CommentFormType($this->entityManager);
        $this->commentFormType->setWordCensor($this->wordCensor);
    }

    public function testCreateUpdateSuccess(): void
    {
        $_POST = [
            'original' => 'Original comment',
            'status' => 1,
            'user_id' => 1,
            'topic_id' => 1
        ];

        $mockTopic = new Topic();
        $mockTopic->setId(1);
        $mockUser = new User();
        $mockUser->setId(1);

        $this->topicRepository->method('find')->willReturn($mockTopic);
        $this->userRepository->method('find')->willReturn($mockUser);

        $comment = new Comment();
        $result = $this->commentFormType->createUpdate($this->entityManager, $comment);

        $this->assertArrayHasKey('entity', $result);
        $this->assertArrayHasKey('error', $result);
        $this->assertEmpty($result['error']);
        $this->assertInstanceOf(Comment::class, $result['entity']);
    }

    protected function tearDown(): void
    {
        $_POST = [];
    }
}

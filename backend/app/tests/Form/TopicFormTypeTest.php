<?php


use App\Entity\Topic;
use App\Entity\User;
use App\Form\TopicFormType;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use App\Repository\UserRepository;

class TopicFormTypeTest extends TestCase
{
    private $entityManager;
    private $topicFormType;
    private $userRepository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->entityManager
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($this->userRepository);

        $this->topicFormType = new TopicFormType();
    }

    public function testCreateUpdateSuccess(): void
    {
        $_POST = [
            'name' => 'Test Topic',
            'description' => 'Test Description',
            'status' => 1,
            'user_id' => 1
        ];

        $mockUser = new User();
        $mockUser->setId(1);
        $this->userRepository->method('find')->willReturn($mockUser);

        $topic = new Topic();
        $result = $this->topicFormType->createUpdate($this->entityManager, $topic);

        $this->assertArrayHasKey('entity', $result);
        $this->assertArrayHasKey('error', $result);
        $this->assertEmpty($result['error']);
        $this->assertInstanceOf(Topic::class, $result['entity']);
        $this->assertEquals('Test Topic', $result['entity']->getName());
        $this->assertEquals('Test Description', $result['entity']->getDescription());
    }

    protected function tearDown(): void
    {
        $_POST = [];
    }
}

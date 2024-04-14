<?php


use App\Entity\DirtyWord;
use App\Form\DirtyWordFormType;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class DirtyWordFormTypeTest extends TestCase
{
    private $entityManager;
    private $dirtyWordFormType;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->dirtyWordFormType = new DirtyWordFormType();
    }

    public function testCreateUpdateSuccess(): void
    {
        $_POST = [
            'word' => 'Test Word',
            'type' => 'Test Type'
        ];

        $dirtyWord = new DirtyWord();

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($dirtyWord));

        $this->entityManager->expects($this->once())
            ->method('flush');

        $result = $this->dirtyWordFormType->createUpdate($this->entityManager, $dirtyWord);

        $this->assertArrayHasKey('entity', $result);
        $this->assertArrayHasKey('error', $result);
        $this->assertEmpty($result['error']);
        $this->assertInstanceOf(DirtyWord::class, $result['entity']);
        $this->assertEquals('Test Word', $result['entity']->getWord());
        $this->assertEquals('Test Type', $result['entity']->getType());
    }

    protected function tearDown(): void
    {
        $_POST = [];
    }
}

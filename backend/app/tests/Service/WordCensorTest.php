<?php

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Service\WordCensor;
use App\Entity\DirtyWord;
use App\Repository\DirtyWordRepository;
use Doctrine\ORM\EntityManagerInterface;

class WordCensorTest extends TestCase
{
    private $entityManagerMock;
    private $dirtyWordRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManagerMock = $this->getMockBuilder(EntityManagerInterface::class)
            ->getMock();

        $this->dirtyWordRepositoryMock = $this->getMockBuilder(DirtyWordRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testCensorWords()
    {
        $text = "This is a dirty text with bad words.";
        $expectedResult = "This is a ***** text with *** words.";

        $dirtyWords = [
            (new DirtyWord())->setWord('dirty'),
            (new DirtyWord())->setWord('bad')
        ];

        $this->dirtyWordRepositoryMock->expects($this->once())
            ->method('findAll')
            ->willReturn($dirtyWords);

        $this->entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->dirtyWordRepositoryMock);

        $wordCensor = new WordCensor($this->entityManagerMock);
        $this->assertEquals($expectedResult, $wordCensor->censorWords($text));
    }
}

<?php

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Service\XmlProcessor;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\DirtyWord;

class XmlProcessorTest extends TestCase
{
    private $entityManagerMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManagerMock = $this->getMockBuilder(EntityManagerInterface::class)
            ->getMock();
    }

    public function testProcess()
    {
        $xmlPath = __DIR__ . '/test_words.xml';
        $xmlContents = '
            <Words>
                <Word type="noun">word1</Word>
                <Word type="adjective">word2</Word>
                <Word type="verb">word3</Word>
            </Words>
        ';
        file_put_contents($xmlPath, $xmlContents);
        $this->entityManagerMock->expects($this->exactly(3))
            ->method('persist')
            ->withConsecutive(
                [$this->callback(function ($arg) {
                    return $arg instanceof DirtyWord &&
                        $arg->getWord() === 'word1' &&
                        $arg->getType() === 'noun';
                })],
                [$this->callback(function ($arg) {
                    return $arg instanceof DirtyWord &&
                        $arg->getWord() === 'word2' &&
                        $arg->getType() === 'adjective';
                })],
                [$this->callback(function ($arg) {
                    return $arg instanceof DirtyWord &&
                        $arg->getWord() === 'word3' &&
                        $arg->getType() === 'verb';
                })]
            );

        $this->entityManagerMock->expects($this->once())
            ->method('flush');
        $xmlProcessor = new XmlProcessor($this->entityManagerMock);
        $xmlProcessor->process($xmlPath);
        unlink($xmlPath);
    }
}

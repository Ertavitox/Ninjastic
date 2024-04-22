<?php

use App\Entity\DirtyWord;
use PHPUnit\Framework\TestCase;

class DirtyWordTest extends TestCase
{
    private DirtyWord $dirtyWord;

    protected function setUp(): void
    {
        $this->dirtyWord = new DirtyWord();
    }

    public function testSetAndGetId(): void
    {
        $id = 1;
        $this->dirtyWord->setId($id);
        $this->assertEquals($id, $this->dirtyWord->getId());
    }

    public function testSetAndGetWord(): void
    {
        $word = 'example';
        $this->dirtyWord->setWord($word);
        $this->assertEquals($word, $this->dirtyWord->getWord());
    }

    public function testSetAndGetType(): void
    {
        $type = 'f';
        $this->dirtyWord->setType($type);
        $this->assertEquals($type, $this->dirtyWord->getType());
    }

    public function testShowTypeText(): void
    {
        $this->dirtyWord->setType('f');
        $this->assertEquals('Force', $this->dirtyWord->showTypeText());

        $this->dirtyWord->setType('m');
        $this->assertEquals('Maybe', $this->dirtyWord->showTypeText());
    }
}

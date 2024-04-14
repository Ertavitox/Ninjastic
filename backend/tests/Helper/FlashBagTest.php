<?php

use App\Helper\FlashBag;
use PHPUnit\Framework\TestCase;

class FlashBagTest extends TestCase
{
    protected function setUp(): void
    {
        if (!isset($_SESSION)) {
            $_SESSION = [];
        }
    }

    public function testSetAndGetFlashBag(): void
    {
        $flashData = ['message' => 'Test message'];
        FlashBag::set('test', $flashData);
        $this->assertTrue(FlashBag::has('test'));
        $data = FlashBag::get('test');

        $this->assertEquals(['message' => 'Test message', 'title' => 'Admin message'], $data);
        $this->assertFalse(FlashBag::has('test'));
    }

    public function testDefaultGetValue(): void
    {
        $defaultValue = 'default';
        $data = FlashBag::get('nonexistent', $defaultValue);

        $this->assertEquals($defaultValue, $data);
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }
}

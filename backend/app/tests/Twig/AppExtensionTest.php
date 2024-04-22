<?php

namespace App\Tests\Twig;

use App\Twig\AppExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class AppExtensionTest extends TestCase
{

    private $appExtension;

    public function setUp(): void
    {
        $request = new Request([], [], [], [], [], ['REQUEST_URI' => '/test']);
        $requestStack = new RequestStack();
        $requestStack->push($request);
        $this->appExtension = new AppExtension($requestStack);
        parent::setUp();
    }

    public function testGetTests()
    {
        $tests = $this->appExtension->getTests();

        $this->assertCount(2, $tests);

        foreach ($tests as $test) {
            $this->assertInstanceOf(\Twig\TwigTest::class, $test);
        }
    }

    public function testGetFunctions()
    {
        $functions = $this->appExtension->getFunctions();

        $this->assertCount(3, $functions);

        foreach ($functions as $function) {
            $this->assertInstanceOf(\Twig\TwigFunction::class, $function);
        }
    }

    public function testGetHostNameMain()
    {
        $hostName = $this->appExtension->getHostNameMain();

        $this->assertEquals('Ninjastic Admin', $hostName);
    }

    public function testCheckStayPage()
    {
        $_POST['stay'] = 1;
        $result = AppExtension::checkStayPage();
        $this->assertFalse($result);

        unset($_POST['stay']);
        $result = AppExtension::checkStayPage();
        $this->assertTrue($result);

        $_POST['stay'] = 0;
        $result = AppExtension::checkStayPage();
        $this->assertTrue($result);
    }

    public function testPathUrl()
    {
        //Given
        $haystack = $this->appExtension->pathUrl();
        $suffix = '/admin/index.php?';

        // When
        $result = substr($haystack, -strlen($suffix)) === $suffix;

        // Then
        $this->assertTrue($result);
    }
}

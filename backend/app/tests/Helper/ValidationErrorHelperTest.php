<?php

use App\Helper\ValidationErrorHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidationErrorHelperTest extends TestCase
{
    public function testGetTransformedErrors(): void
    {
        $violation1 = $this->createMock(ConstraintViolation::class);
        $violation1->method('getMessage')->willReturn('Error message 1');
        $violation1->method('getPropertyPath')->willReturn('field1');

        $violation2 = $this->createMock(ConstraintViolation::class);
        $violation2->method('getMessage')->willReturn('Error message 2');
        $violation2->method('getPropertyPath')->willReturn('field2');

        $violations = new ConstraintViolationList([$violation1, $violation2]);
        $validationErrorHelper = new ValidationErrorHelper($violations);

        $expectedErrors = [
            ['message' => 'Error message 1', 'key' => 'field1'],
            ['message' => 'Error message 2', 'key' => 'field2']
        ];

        $this->assertEquals($expectedErrors, $validationErrorHelper->getTransformedErrors());
    }
}

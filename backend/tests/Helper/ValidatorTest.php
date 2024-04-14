<?php

use App\Helper\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    public function testAddRuleAndValidate(): void
    {
        $post = ['email' => 'test@example.com'];
        $validator = new Validator($post);
        $validator->addRule('email', 'email');

        $this->assertTrue($validator->clearAndValidate());
    }

    public function testAddFilterAndValidate(): void
    {
        $post = ['name' => ' test '];
        $validator = new Validator($post);
        $validator->addFilter('name', 'trim');

        $validator->clearAndValidate();
        $cleanedValues = $validator->getCleanedValues();

        $this->assertEquals('test', $cleanedValues['name']);
    }

    public function testAddDefaultRuleAndValidate(): void
    {
        $post = ['age' => '30'];
        $validator = new Validator($post);
        $validator->addDefaultRule('number');

        $this->assertTrue($validator->clearAndValidate());
    }

    public function testValidationErrors(): void
    {
        $post = ['email' => 'invalid-email'];
        $validator = new Validator($post);
        $validator->addRule('email', 'email');

        $isValid = $validator->clearAndValidate();
        $errors = $validator->getListOfErrors();

        $this->assertFalse($isValid);
        $this->assertArrayHasKey('email', $errors);
    }

    public function testNotEmptyValidation()
    {
        $validator = new Validator(['name' => '']);
        $validator->addRule('name', 'notEmpty');
        $this->assertFalse($validator->clearAndValidate());
    }

    public function testNumberValidation()
    {
        $validator = new Validator(['age' => '30']);
        $validator->addRule('age', 'number');
        $this->assertTrue($validator->clearAndValidate());
    }

    public function testTrimFilter()
    {
        $validator = new Validator(['name' => ' John ']);
        $validator->addFilter('name', 'trim');
        $validator->clearAndValidate();
        $this->assertEquals('John', $validator->getCleanedValues()['name']);
    }

    public function testStripTagsFilter()
    {
        $validator = new Validator(['comment' => '<p>Test</p>']);
        $validator->addFilter('comment', 'stripTags');
        $validator->clearAndValidate();
        $this->assertEquals('Test', $validator->getCleanedValues()['comment']);
    }

    public function testUnFormatCSVPriceFilter()
    {
        $validator = new Validator(['price' => '1,000']);
        $validator->addFilter('price', 'unFormatCSVPrice');
        $validator->clearAndValidate();
        $this->assertEquals('1.000', $validator->getCleanedValues()['price']);
    }

    public function testCreditCardValidation()
    {
        $validator = new Validator(['card' => '4111111111111111']);
        $validator->addRule('card', 'creditCard');
        $this->assertTrue($validator->clearAndValidate());
    }

    public function testPhoneLengthValidation()
    {
        $validator = new Validator(['phone' => '1234567']);
        $validator->addRule('phone', 'phone');
        $this->assertTrue($validator->clearAndValidate());
    }

    public function testErrorList()
    {
        $validator = new Validator(['email' => 'not-an-email']);
        $validator->addRule('email', 'email');
        $validator->clearAndValidate();
        $this->assertNotEmpty($validator->getListOfErrors());
    }

    public function testErrorMessages()
    {
        $validator = new Validator(['input' => '']);
        $validator->addRule('input', 'notEmpty', ['Input field cannot be empty']);
        $validator->clearAndValidate();
        $this->assertEquals('notEmpty', $validator->getListOfErrors()['input'][0]);
    }

    public function testEmailValidation()
    {
        $validator = new Validator(['email' => 'test@example.com']);
        $validator->addRule('email', 'email');
        $this->assertTrue($validator->clearAndValidate());
    }

    public function testAlphaValidation()
    {
        $validator = new Validator(['input' => 'abcDEF']);
        $validator->addRule('input', 'alpha');
        $this->assertTrue($validator->clearAndValidate());
    }

    public function testAlphaNumericValidation()
    {
        $validator = new Validator(['input' => 'abc123']);
        $validator->addRule('input', 'alphaNumeric');
        $this->assertTrue($validator->clearAndValidate());
    }

    public function testDateValidation()
    {
        $validator = new Validator(['date' => '2024-04-08']);
        $validator->addRule('date', 'date');
        $this->assertTrue($validator->clearAndValidate());
    }

    public function testUrlValidation()
    {
        $validator = new Validator(['url' => 'http://example.com']);
        $validator->addRule('url', 'url');
        $this->assertTrue($validator->clearAndValidate());
    }

    public function testMinLengthValidation()
    {
        $validator = new Validator(['input' => 'abc']);
        $validator->addRule('input', 'minLength', [2]);
        $this->assertTrue($validator->clearAndValidate());
    }

    public function testMaxLengthValidation()
    {
        $validator = new Validator(['input' => 'abc']);
        $validator->addRule('input', 'maxLength', [5]);
        $this->assertTrue($validator->clearAndValidate());
    }
}

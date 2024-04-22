<?php

use PHPUnit\Framework\TestCase;
use App\Helper\ValidatorEConvMessage;

class ValidatorEConvMessageTest extends TestCase
{
    public function testReplaceErrorToMessage()
    {
        $validator = new ValidatorEConvMessage();

        $this->assertEquals([], $validator->replaceErrorToMessage([]));

        $errors = ['notEmpty', 'maxLength'];
        $expectedResult = [
            'notEmpty' => 'Field cannot be empty!',
            'maxLength' => 'Field value is too long!'
        ];
        $this->assertEquals($expectedResult["notEmpty"], $validator->replaceErrorToMessage($errors)[0]);
        $this->assertEquals($expectedResult["maxLength"], $validator->replaceErrorToMessage($errors)[1]);

        $errorsWithUnknownCode = ['notEmpty', 'unknownCode'];
        $expectedResultWithUnknownCode = [
            'notEmpty' => 'Field cannot be empty!',
            'unknownCode' => 'unknownCode'
        ];

        $this->assertEquals($expectedResultWithUnknownCode["notEmpty"], $validator->replaceErrorToMessage($errorsWithUnknownCode)[0]);
        $this->assertEquals($expectedResultWithUnknownCode["unknownCode"], $validator->replaceErrorToMessage($errorsWithUnknownCode)[1]);
    }
}

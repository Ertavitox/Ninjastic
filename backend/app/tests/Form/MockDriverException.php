<?php

namespace App\Tests\Form;

use Doctrine\DBAL\Driver\Exception as DriverException;
use Exception;

class MockDriverException extends Exception implements DriverException
{
    public function __construct($message = "", $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getSQLState(): ?string
    {
        return '23000';
    }
}

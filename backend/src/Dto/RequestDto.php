<?php

namespace App\Dto;

use OpenApi\Attributes as OA;

class RequestDto
{
    public string $message;

    public array $errors;
    public mixed $result;

    public function __construct(string $message = '', array $errors = [], mixed $result = null)
    {
        $this->message = $message;
        $this->errors = $errors;
        $this->result = $result;
    }

}
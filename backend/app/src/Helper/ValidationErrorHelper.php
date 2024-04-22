<?php

namespace App\Helper;

use Symfony\Component\Validator\ConstraintViolationList;

class ValidationErrorHelper
{

    private ConstraintViolationList $validationErrors;

    public function __construct(ConstraintViolationList $validationErrors)
    {
        $this->validationErrors = $validationErrors;
    }

    public function getTransformedErrors()
    {
        $errors = [];
        foreach ($this->validationErrors as $error) {
            $errors[] = [
                'message' =>  $error->getMessage(),
                'key' => $error->getPropertyPath(),
            ];
        }

        return $errors;
    }
}
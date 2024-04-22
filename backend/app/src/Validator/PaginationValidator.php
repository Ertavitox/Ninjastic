<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;

class PaginationValidator
{
    #[Assert\Type('integer')]
    #[Assert\GreaterThanOrEqual(1)]
    public readonly int $page;

    #[Assert\Type('integer')]
    #[Assert\GreaterThanOrEqual(1)]
    #[Assert\LessThanOrEqual(100)]
    public readonly int $limit;

    public function __construct(int $page, int $limit)
    {
        $this->page = $page;
        $this->limit = $limit;
    }
}

<?php

namespace App\Validator\Comments;

use Symfony\Component\Validator\Constraints as Assert;

class CommentsPaginationValidator
{
    #[Assert\Type('integer')]
    #[Assert\GreaterThanOrEqual(1)]
    public readonly int $page;

    #[Assert\Type('integer')]
    public readonly int $topicId;

    #[Assert\Type('integer')]
    #[Assert\GreaterThanOrEqual(1)]
    #[Assert\LessThanOrEqual(100)]
    public readonly int $limit;

    public function __construct(int $topicId, int $page, int $limit)
    {
        $this->topicId = $topicId;
        $this->page = $page;
        $this->limit = $limit;
    }
}

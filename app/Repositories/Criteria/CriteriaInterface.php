<?php

declare(strict_types=1);

namespace App\Repositories\Criteria;

interface CriteriaInterface
{
    /**
     * @param  array<mixed>  $criteria
     */
    public function withCriteria(...$criteria): self;
}

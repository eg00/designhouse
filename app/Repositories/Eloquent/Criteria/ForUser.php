<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;
use Illuminate\Database\Eloquent\Builder;

class ForUser implements CriterionInterface
{
    /**
     * ForUser constructor.
     */
    public function __construct(protected int $user_id)
    {
    }

    public function apply(Builder $builder): Builder
    {
        return $builder->where('user_id', $this->user_id);
    }
}

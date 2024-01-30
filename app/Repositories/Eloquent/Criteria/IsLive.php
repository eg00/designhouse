<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Criteria;

use Illuminate\Database\Eloquent\Builder;

class IsLive implements \App\Repositories\Criteria\CriterionInterface
{
    public function apply(Builder $builder): Builder
    {
        return $builder->where('is_live', true);
    }
}

<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;
use Illuminate\Database\Eloquent\Builder;

class LatestFirst implements CriterionInterface
{
    public function apply(Builder $builder): Builder
    {
        return $builder->latest();
    }
}

<?php

declare(strict_types=1);

namespace App\Repositories\Criteria;

use Illuminate\Database\Eloquent\Builder;

interface CriterionInterface
{
    public function apply(Builder $model): Builder;
}

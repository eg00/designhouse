<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;
use Illuminate\Database\Eloquent\Builder;

class EagerLoad implements CriterionInterface
{
    /**
     * @param  array<string>  $relationships
     */
    public function __construct(protected array $relationships)
    {
    }

    public function apply(Builder $builder): Builder
    {
        return $builder->with($this->relationships);
    }
}

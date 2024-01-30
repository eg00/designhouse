<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EagerLoad implements CriterionInterface
{
    /**
     * @param  array<string>  $relationships
     */
    public function __construct(protected array $relationships)
    {
    }

    public function apply(Model|Builder $model): Builder
    {
        return $model->with($this->relationships);
    }
}

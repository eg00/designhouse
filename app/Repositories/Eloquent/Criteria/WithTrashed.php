<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class WithTrashed implements CriterionInterface
{
    public function apply(Model|Builder $model): Builder
    {
        return $model->withTrashed();
    }
}

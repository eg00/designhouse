<?php

namespace App\Repositories\Eloquent\Criteria;

class IsLive implements \App\Repositories\Criteria\CriterionInterface
{
    public function apply($model)
    {
        return $model->where('is_live', true);
    }
}

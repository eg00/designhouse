<?php


namespace App\Repositories\Eloquent\Criteria;


class LatestFirst implements \App\Repositories\Criteria\CriterionInterface
{

    public function apply($model)
    {
        return $model->latest();
    }
}

<?php


namespace App\Repositories\Eloquent\Criteria;


class WithTrashed implements \App\Repositories\Criteria\CriterionInterface
{

    public function apply($model)
    {
        return $model->withTrashed();
    }
}

<?php

namespace App\Repositories\Eloquent\Criteria;

class EagerLoad implements \App\Repositories\Criteria\CriterionInterface
{
    protected $relationships;

    /**
     * ForUser constructor.
     */
    public function __construct($relationships)
    {
        $this->relationships = $relationships;
    }

    public function apply($model)
    {
        return $model->with($this->relationships);
    }
}

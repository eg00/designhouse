<?php


namespace App\Repositories\Eloquent\Criteria;


use App\Repositories\Criteria\CriterionInterface;

class ForUser implements CriterionInterface
{

    protected $user_id;

    /**
     * ForUser constructor.
     * @param $user_id
     */
    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }


    public function apply($model)
    {
        return $model->where('user_id', $this->user_id);
    }
}

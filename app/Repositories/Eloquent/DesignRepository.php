<?php


namespace App\Repositories\Eloquent;


use App\Models\Design;
use App\Repositories\Contracts\DesignInterface;

class DesignRepository implements DesignInterface
{

    public function all()
    {
        return Design::all();
    }
}

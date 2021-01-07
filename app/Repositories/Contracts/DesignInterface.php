<?php

namespace App\Repositories\Contracts;

/**
 * @method withCriteria(array $array)
 */
interface DesignInterface extends BaseInterface
{
    public function ApplyTags($id,array $data);
}

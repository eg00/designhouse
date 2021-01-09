<?php

namespace App\Repositories\Contracts;

/**
 * @method withCriteria(array $array)
 */
interface DesignInterface extends BaseInterface
{
    public function ApplyTags($id, array $data);

    public function addComment($design_id, array $data);

    public function like($id);

    public function isLikedByUser($design_id);
}

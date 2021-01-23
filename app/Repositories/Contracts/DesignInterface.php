<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

/**
 * @method withCriteria(array $array)
 */
interface DesignInterface extends BaseInterface
{
    public function ApplyTags($id, array $data);

    public function addComment($design_id, array $data);

    public function like($id);

    public function isLikedByUser($design_id);

    public function search(Request $request);
}

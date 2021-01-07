<?php

namespace App\Repositories\Contracts;

interface DesignInterface extends BaseInterface
{
    public function ApplyTags($id,array $data);
}

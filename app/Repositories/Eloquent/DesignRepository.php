<?php


namespace App\Repositories\Eloquent;


use App\Models\Design;
use App\Repositories\Contracts\DesignInterface;

class DesignRepository extends BaseRepository implements DesignInterface
{

    public function model()
    {
        return Design::class;
    }

    public function ApplyTags($id, array $data)
    {
        $design = $this->find($id);
        $design->retag($data);
    }

    public function addComment($design_id, array $data)
    {
        $design = $this->find($design_id);

        return $design->comments()->create($data);
    }

}

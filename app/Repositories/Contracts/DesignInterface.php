<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * @method withCriteria(array $array)
 */
interface DesignInterface extends BaseInterface
{
    /**
     * @param  array<mixed>  $data
     */
    public function ApplyTags(int $id, array $data): void;

    /**
     * @param  array<mixed>  $data
     */
    public function addComment(int $design_id, array $data): Comment;

    public function like(int $id): void;

    public function isLikedByUser(int $design_id): bool;

    public function search(Request $request): Collection;
}

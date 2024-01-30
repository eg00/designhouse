<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface BaseInterface
{
    public function all(): Collection;

    public function find(int|string $id): Model;

    public function findWhere(string $column, mixed $value): Collection;

    public function findWhereFirst(string $column, mixed $value): Model;

    public function paginate(int $perPage = 10): LengthAwarePaginator;

    /**
     * @param  array<mixed>  $data
     */
    public function create(array $data): Model;

    /**
     * @param  array<mixed>  $data
     */
    public function update(int $id, array $data): Model;

    public function delete(int $id): ?bool;
}

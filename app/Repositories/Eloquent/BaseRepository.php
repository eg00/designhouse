<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Exceptions\ModelNotDefined;
use App\Repositories\Contracts\BaseInterface;
use App\Repositories\Criteria\CriteriaInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

abstract class BaseRepository implements BaseInterface, CriteriaInterface
{
    protected Model|Builder $model;

    public function __construct()
    {
        $this->model = $this->getModelClass();
    }

    protected function getModelClass(): Model|Builder
    {
        if (!method_exists($this, 'model')) {
            throw new ModelNotDefined();
        }

        return $this->model();
    }

    public function all(): Collection
    {
        return $this->model->get();
    }

    public function findWhere(string $column, mixed $value): Collection
    {
        return $this->model->where($column, $value)->get();
    }

    public function findWhereFirst(string $column, mixed $value): Model
    {
        return $this->model->where($column, $value)->firstOrFail();
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    /**
     * @param array<mixed> $data
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * @param array<mixed> $data
     */
    public function update(int $id, array $data): Model
    {
        $record = $this->find($id);
        $record->update($data);

        return $record;
    }

    public function find(int|string $id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function delete(int $id): ?bool
    {
        return $this->find($id)->delete();
    }

    /**
     * @param array<mixed> $criteria
     */
    public function withCriteria(...$criteria): self
    {
        $criteria = Arr::flatten($criteria);

        foreach ($criteria as $criterion) {
            $this->model = $criterion->apply($this->model);
        }

        return $this;
    }
}

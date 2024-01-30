<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\ModelNotDefined;
use App\Repositories\Contracts\BaseInterface;
use App\Repositories\Criteria\CriteriaInterface;
use Illuminate\Support\Arr;

abstract class BaseRepository implements BaseInterface, CriteriaInterface
{
    protected $model;

    public function __construct()
    {
        $this->model = $this->getModelClass();
    }

    protected function getModelClass()
    {
        if (! method_exists($this, 'model')) {
            throw new ModelNotDefined();
        }

        return app()->make($this->model());
    }

    public function all()
    {
        return $this->model->get();
    }

    public function findWhere(string $column, $value)
    {
        return $this->model->where($column, $value)->get();
    }

    public function findWhereFirst(string $column, $value)
    {
        return $this->model->where($column, $value)->firstOrFail();
    }

    public function paginate($perPage = 10)
    {
        return $this->model->paginate($perPage);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $record = $this->find($id);
        $record->update($data);

        return $record;
    }

    public function find(int|string $id)
    {
        return $this->model->findOrFail($id);
    }

    public function delete(int $id)
    {
        $record = $this->find($id);

        return $record->delete();
    }

    public function withCriteria(...$criteria)
    {
        $criteria = Arr::flatten($criteria);

        foreach ($criteria as $criterion) {
            $this->model = $criterion->apply($this->model);
        }

        return $this;
    }
}

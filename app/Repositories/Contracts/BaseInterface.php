<?php


namespace App\Repositories\Contracts;


interface BaseInterface
{
    public function all();

    public function find(int|string $id);

    public function findWhere(string $column, $value);

    public function findWhereFirst(string $column, $value);

    public function paginate($perPage = 10);

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);
}

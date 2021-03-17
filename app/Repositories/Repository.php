<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class Repository implements IRepository {

    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function get($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(Model $record, array $data)
    {
        return $record->update($data);
    }

    public function delete(Model $record)
    {
        return $record->delete();
    }
}

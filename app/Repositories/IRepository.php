<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

interface IRepository {
    public function getAll();
    public function get($id);
    public function create(array $data);
    public function update(Model $record, array $data);
    public function delete(Model $record);
}

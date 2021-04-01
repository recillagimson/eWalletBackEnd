<?php
namespace App\Services\NewsAndUpdate;

interface INewsAndUpdateService 
{
    public function index();
    public function createRecord(array $details);
    public function show(string $id);
    public function update(string $id, array $details);
    public function delete(string $id);
}

<?php
namespace App\Services\TempUserDetail;

use App\Models\TempUserDetail;
use Illuminate\Database\Eloquent\Collection;

interface ITempUserDetailService
{
    public function getAllPaginated($attributes, $perPage = 10);
    public function findById(string $id);
    public function updateStatus(string $id, $status, object $user);

}

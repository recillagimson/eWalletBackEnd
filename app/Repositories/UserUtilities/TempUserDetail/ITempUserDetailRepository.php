<?php

namespace App\Repositories\UserUtilities\TempUserDetail;

use App\Models\TempUserDetail;
use App\Repositories\IRepository;

interface ITempUserDetailRepository extends IRepository
{
    public function getAllPaginated(string $userId);
    public function findById(string $userId);
    public function getLatestByUserId(string $id);
    public function denyByUserId(string $id, object $user);
    public function getTempUserDetails();
}

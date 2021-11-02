<?php
namespace App\Repositories\DrcrMemoControlNumber;

use App\Models\UserAccount;
use App\Repositories\IRepository;

interface IDrcrMemoControlNumberRepository extends IRepository
{
    public function findByControlNumber($controlNumber);
}

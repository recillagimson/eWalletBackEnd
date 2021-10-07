<?php
namespace App\Repositories\InReceiveFromDBP;

use App\Repositories\IRepository;
use App\Models\InReceiveFromDBP;

interface IInReceiveFromDBPRepository extends IRepository
{
    public function getExistByTransactionCategory($rsbsaNumber, $transactionCategory);
}

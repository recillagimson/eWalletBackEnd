<?php


namespace App\Repositories\UserAccountNumber;


use App\Repositories\IRepository;

interface IUserAccountNumberRepository extends IRepository
{
    public function generateNo(string $prefix = ''): string;
}

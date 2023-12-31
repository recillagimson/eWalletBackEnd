<?php

namespace App\Repositories\UserUtilities\MaritalStatus;

use App\Repositories\IRepository;

interface IMaritalStatusRepository extends IRepository
{
    public function getMaritalStatuses(); 
    public function getByDescription($value); 
}

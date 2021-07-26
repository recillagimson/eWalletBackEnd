<?php

namespace App\Repositories\UserUtilities\Nationality;

use App\Repositories\IRepository;

interface INationalityRepository extends IRepository
{
    public function getAllNationalities();
}

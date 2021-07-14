<?php

namespace App\Repositories\Address\Municipality;

use App\Repositories\IRepository;

interface IMunicipalityRepository extends IRepository
{
    public function getMunicipalities(string $code);
}

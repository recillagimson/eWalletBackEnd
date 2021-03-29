<?php


namespace App\Repositories\OtpRepository;

use App\Repositories\IRepository;

interface IOtpRepository extends IRepository
{
    public function getByIdentifier(string $identifier);

    public function deleteOld();
}

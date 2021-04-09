<?php


namespace App\Repositories\OtpRepository;

use App\Repositories\IRepository;

interface IOtpRepository extends IRepository
{
    public function getByIdentifier(string $identifier, bool $validated = false);

    public function deleteOld();
}

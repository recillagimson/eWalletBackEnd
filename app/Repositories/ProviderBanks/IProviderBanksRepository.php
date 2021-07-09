<?php


namespace App\Repositories\ProviderBanks;


use App\Repositories\IRepository;
use Illuminate\Database\Eloquent\Collection;

interface IProviderBanksRepository extends IRepository
{
    public function getPesonetBanks(): Collection;
}

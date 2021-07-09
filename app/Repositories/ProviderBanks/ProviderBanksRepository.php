<?php


namespace App\Repositories\ProviderBanks;


use App\Enums\TpaProviders;
use App\Models\ProviderBank;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\Collection;

class ProviderBanksRepository extends Repository implements IProviderBanksRepository
{
    public function __construct(ProviderBank $model)
    {
        parent::__construct($model);
    }

    public function getPesonetBanks(): Collection
    {
        return $this->model
            ->where('provider', TpaProviders::secBankPesonet)
            ->orderBy('name')
            ->get();
    }
}

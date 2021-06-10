<?php


namespace App\Repositories\UserAccountNumber;


use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UserAccountNumberRepository extends Repository implements IUserAccountNumberRepository
{
    public function __construct(IUserAccountNumberRepository $model)
    {
        parent::__construct($model);
    }

    public function generateNo(): string
    {
        $currentDate = Carbon::now()->toDateString();
        $accountCounter = $this->model->where('account_date', '=', $currentDate)
            ->lockForUpdate()->first();

        if ($accountCounter) {
            $accountCounter->counter += 1;
            $accountCounter->save();
        } else {
            $accountCounter = $this->model->create([
                'account_date' => $currentDate,
                'counter' => 1
            ]);
        }

        $strAccountDate = $accountCounter->account_date->toString('Ymd');
        $strNo = Str::padLeft($accountCounter->counter, 6, '0');
        return $strAccountDate . $strNo;
    }
}

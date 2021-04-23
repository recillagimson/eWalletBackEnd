<?php

namespace App\Repositories\Tier;

use App\Models\Tier;
use App\Models\UserAccount;
use App\Repositories\Repository;
use Illuminate\Validation\ValidationException;

class TierRepository extends Repository implements ITierRepository
{
    public function __construct(Tier $model)
    {
        parent::__construct($model);
    }

    public function list($params = []) {
        if(isset($params['search']) && $params['search'] != "") {
            return $this->model->where('name', 'LIKE', '%' . $params['search'] . '%')->get();
        } else {
            return $this->model->get();
        }
    }

    public function getTierByUserAccountId($userAccountId) {
        $user = UserAccount::find($userAccountId);
        if($user) {
            $tier = $this->model->find($user->tier_id);
            if($tier) {
                return $tier;
            }
            throw ValidationException::withMessages([
                'tier_not_found' => 'Tier not found or not Set'
            ]);
        }
        throw ValidationException::withMessages([
            'user_not_found' => 'Account not found'
        ]);
    }

}

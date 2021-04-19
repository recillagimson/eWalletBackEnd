<?php

namespace App\Repositories\ServiceFee;

use App\Models\ServiceFee;
use App\Models\UserAccount;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ServiceFeeRepository extends Repository implements IServiceFeeRepository
{
    public function __construct(ServiceFee $model)
    {
        parent::__construct($model);
    }

    /**
     * Params
     *
     * search (optional)
     * provides list of service fee records
     * can be search by tier name
     * 
     * @return bool
     */
    public function list($params = []) {
        // Check if has search parameter
        if(isset($params['search']) && $params['search'] != "") {
            // user relation to search for matching tier name
            return $this->model->whereHas('tier', function($query) use($params) {
                // Search by tier name
                $query->where('name', 'LIKE', '%' . $params['search'] . '%');
            })->get();
        } 
        // no search parameter so do normal retrieval of data
        else {
            return $this->model->get();
        }
    }

    /**
     * Get the service fee using the tier_id and 
     *
     * @param uuid $tier_id
     * @param uuid $tranCategoryID
     * @return SeviceFee
     */
    public function getByTierAndTransCategoryID(string $tier_id, string $tranCategoryID)
    {
        return $this->model->where('tier_id', $tier_id)->where('transaction_category_id', $tranCategoryID)->first();
    }

    public function getAmountByTransactionAndUserAccountId(string $transactionCategoryId, string $userAccountId) {
        // Get User 
        $user = UserAccount::with(['tier'])->where('id', $userAccountId)->first();
        if($user) {
            $amount = $this->model->whereHas('tier', function($query) use($user) {
                $query->where('id', $user->tier_id);        
            })
            ->select(['id', 'amount'])
            ->where('transaction_category_id', $transactionCategoryId)
            ->first();
            
            if($amount) {
                return $amount;
            }
        }

        // throw error if not found
        throw new ModelNotFoundException('Service Fee record not found');
    }
}

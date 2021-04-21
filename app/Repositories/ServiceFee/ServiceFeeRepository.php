<?php

namespace App\Repositories\ServiceFee;

use App\Models\ServiceFee;
use App\Models\UserAccount;
use Illuminate\Support\Carbon;
use App\Repositories\Repository;
use Illuminate\Validation\ValidationException;
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

    public function getByTierAndTransCategoryID(int $tier, string $tranCategoryID)
    {
        return $this->model->where('tier', $tier)->where('transaction_category_id', $tranCategoryID)->first();
    }

    public function getAmountByTransactionAndTierId(string $transactionCategoryId, string $tierId) {
        // Get User 
        $currentDate = Carbon::now()->format('Y-m-d');

        $amount = $this->model
        ->select(['id', 'amount'])
        ->where('tier_id', $tierId)
        ->where('transaction_category_id', $transactionCategoryId)
        ->where('implementation_date', '>=', $currentDate)
        ->first();
            
        if($amount) {
            return $amount;
        }

        // throw error if not found
        throw ValidationException::withMessages([
            'tier_not_found' => 'Tier is not found'
        ]);
    }
}

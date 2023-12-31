<?php

namespace App\Repositories\ServiceFee;

use App\Models\ServiceFee;
use App\Repositories\Repository;
use Illuminate\Support\Carbon;

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
     * @param string $tierID
     * @param string $transCategoryID
     * @return mixed
     */
    public function getByTierAndTransCategory(string $tierID, string $transCategoryID)
    {
        return $this->model
            ->where('tier_id', $tierID)
            ->where('transaction_category_id', $transCategoryID)
            ->first();
    }

    public function getAmountByTransactionAndUserAccountId(string $transactionCategoryId, string $tierId)
    {
        // Get User
        // get Current Date for query parameter for implementation date
        $currentDate = Carbon::now()->format('Y-m-d');

        $amount = $this->model
            ->select(['id', 'amount'])
            ->where('tier_id', $tierId)
            ->where('transaction_category_id', $transactionCategoryId)
            ->where('implementation_date', '<=', $currentDate)
            ->orderBy('created_at', 'DESC')
            ->first();

        if($amount) {
            return $amount;
        }

        // Fix issue raised by Davette
        return 0;

        // throw error if not found
        // throw ValidationException::withMessages([
        //     'tier_not_found' => 'Tier is not found'
        // ]);
    }

    public function getServiceFeeByTransactionCategoryId(string $transactionCategoryId) {
        return $this->model->where('transaction_category_id', $transactionCategoryId)->first();
    }
}

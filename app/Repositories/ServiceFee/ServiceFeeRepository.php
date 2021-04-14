<?php

namespace App\Repositories\ServiceFee;

use App\Models\ServiceFee;
use App\Repositories\Repository;

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
}

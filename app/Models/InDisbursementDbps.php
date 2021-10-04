<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InDisbursementDbps extends Model
{
    use HasFactory, UsesUuid;

    protected $table = 'in_disbursement_dbps';

    protected $fillable = [
        "user_account_id",
        "reference_number",
        "total_amount",
        "status",
        "transaction_date",
        "transaction_category_id",
        "transaction_remarks",
        "disbursed_by",
        "user_created",
        "user_updated",
        "out_disbursement_dbps_reference_number"
    ];
}

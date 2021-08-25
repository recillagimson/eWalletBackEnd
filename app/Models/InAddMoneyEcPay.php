<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InAddMoneyEcPay extends Model
{
    use UsesUuid, HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "in_add_money_ec_pays";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "user_account_id",
        "reference_number",
        "amount",
        "service_fee",
        "service_fee_id",
        "total_amount",
        "ec_pay_reference_number",
        "expiry_date",
        "transaction_date",
        "transction_category_id",
        "transaction_remarks",
        "status",
        "user_created",
        "user_updated",
        "updated_at"
    ];
}

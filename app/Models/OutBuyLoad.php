<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutBuyLoad extends Model
{
    use UsesUuid, HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'out_buy_loads';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_account_id',
        'reference_number',
        'total_amount',
        'transaction_date',
        'transaction_category_id',
        'atm_reference_number',
        'recipient_mobile_number',
        'provider',
        'product_code',
        'product_name',
        'transaction_response',
        'status',
        'user_created',
        'user_updated',
    ];

    protected $casts = [
        'transaction_date' => 'datetime'
    ];
}

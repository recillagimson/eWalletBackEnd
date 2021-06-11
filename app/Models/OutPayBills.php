<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutPayBills extends Model
{
    use HasFactory, UsesUuid, SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_account_id',
        'account_number',
        'reference_number',
        'amount',
        'other_charges',
        'service_fee',
        // 'service_fee_id',
        'total_amount',
        'transaction_date',
        'transaction_category_id',
        'transaction_remarks',
        'message',
        'status',
        'client_reference',
        'billers_code',
        'billers_name',
        'biller_reference_number',
        'user_created',
        'user_updated'
    ];

    protected $casts = ['transaction_date' => 'datetime'];

}

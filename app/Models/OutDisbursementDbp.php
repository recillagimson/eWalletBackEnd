<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutDisbursementDbp extends Model
{
    use HasFactory, UsesUuid, SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_account_id',
        'reference_number',
        'total_amount',
        'status',
        'transaction_date',
        'transaction_category_id',
        'transaction_remarks',
        'disbursed_by',
        'user_created',
        'user_updated'
    ];
    
}

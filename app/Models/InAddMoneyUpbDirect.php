<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InAddMoneyUpbDirect extends Model
{
    use HasFactory, UsesUuid, SoftDeletes;

    protected $table = 'in_add_money_upb_direct';

    protected $fillable = [
        'user_account_id',
        'reference_number',
        'total_amount',
        'transaction_date',
        'transaction_category_id',
        'transaction_remarks',
        'status',
        'upb_reference',
        'transaction_response',
        'user_created',
        'user_updated',
    ];
}

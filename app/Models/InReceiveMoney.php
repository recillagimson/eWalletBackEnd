<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InReceiveMoney extends Model
{
   use HasFactory, UsesUuid, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
    */
    protected $fillable = [
        'user_account_id',
        'sender_id',
        'reference_number',
        'out_send_money_reference_number',
        'amount',
        'message',
        'transaction_date',
        'transaction_category_id',
        'transaction_remarks',
        'status',
        'user_created',
        'user_updated',
    ];


}

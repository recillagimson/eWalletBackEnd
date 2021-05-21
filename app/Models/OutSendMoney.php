<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutSendMoney extends Model
{
    use HasFactory, UsesUuid, SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_account_id',
        'receiver_id',
        'reference_number',
        'amount',
        'service_fee',
        // 'service_fee_id',
        'total_amount',
        // 'purpose_of_transfer_id',
        'message',
        'status',
        'transaction_date',
        'transaction_category_id',
        'transaction_remarks',
        'user_created',
        'user_updated'
    ];

    public function receiver() {
        return $this->hasOne(UserAccount::class, 'id', 'receiver_id');
    }

    public function receiver_details() {
        return $this->hasOne(UserDetail::class, 'user_account_id', 'receiver_id');
    }

}

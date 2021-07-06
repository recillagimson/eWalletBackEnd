<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DrcrMemo extends Model
{
    use HasFactory, UsesUuid, SoftDeletes;

    protected $table = 'drcr_memos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_account_id',
        'type_of_memo',
        'reference_number',
        'transaction_category_id',
        'amount',
        'currency_id',
        'category',
        'description',
        'status',
        'created_by',
        'approved_by',
        'declined_by',
        'approved_at',
        'declinet_at',
        'user_created',
        'user_updated'
    ];

    public function user_account() {
        return $this->hasOne(UserAccount::class, 'id', 'user_account_id');
    }

    public function user_details() {
        return $this->hasOne(UserDetail::class, 'user_account_id', 'user_account_id');
    }

    public function user_balance_info() {
        return $this->hasOne(UserBalanceInfo::class, 'user_account_id', 'user_account_id');
    }

}


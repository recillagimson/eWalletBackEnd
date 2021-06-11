<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DrcrMemos extends Model
{
    use HasFactory,  UsesUuid, SoftDeletes;


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
        'created_date',
        'approved_by',
        'approved_date',
        'declined_by',
        'declined_date',
        'user_created',
        'user_updated'
    ];

}

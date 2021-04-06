<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceFee extends Model
{
    use HasFactory, UsesUuid, SoftDeletes;

    protected $fillable = [
        'old_service_fee_id',
        'tier',
        'transaction_category_id',
        'name',
        'amount',
        'implementation_date',
        'user_created',
        'user_updated',
    ];
}

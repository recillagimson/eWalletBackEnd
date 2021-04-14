<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceFee extends Model
{
    use HasFactory, SoftDeletes;
    use UsesUuid;

    protected $table = 'service_fees';

    protected $fillable = [
        "tier_id",
        "transaction_category_id",
        "implementation_date",
        "user_created",
        "user_updated",
    ];
}

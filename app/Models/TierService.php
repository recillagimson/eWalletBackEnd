<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tier;

class TierService extends Model
{
    use UsesUuid;
    use HasFactory;

    protected $table = 'tier_services';

    protected $fillable = [
        'tier_id',
        'transaction_category_id',
        'is_accessible',
        'status',
        'user_created',
        'user_updated',
    ];
}

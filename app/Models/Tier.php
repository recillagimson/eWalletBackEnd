<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\TierService;
use App\Models\TransactionCategory;

class Tier extends Model
{
    use UsesUuid;
    use HasFactory, SoftDeletes;

    protected $table = 'tiers';

    protected $fillable = [
        "name",
        "tier_class",
        "account_status",
        "daily_limit",
        "daily_threshold",
        "monthly_limit",
        "monthly_threshold",
        "status",
        "user_created",
        "user_updated",
    ];

    public function TransactionCategory()
    {
        return $this->belongsToMany(TransactionCategory::class,'tier_services','tier_id','transaction_category_id')
        ->withTimestamps()
        ->withPivot(['is_accessible'])
        ->wherePivot('is_accessible',1);
    }

    public function TierRequirement()
    {
        return $this->hasMany(TierRequirement::class);
    }
}

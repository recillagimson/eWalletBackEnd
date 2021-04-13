<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tier extends Model
{
    use UsesUuid;
    use HasFactory, SoftDeletes;

    protected $table = 'tiers';

    protected $fillable = [
        "name",
        "daily_limit",
        "daily_threshold",
        "monthly_limit",
        "monthly_threshold",
        "status",
        "user_created",
        "user_updated",
    ];
}

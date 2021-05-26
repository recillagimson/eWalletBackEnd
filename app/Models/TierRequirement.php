<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TierRequirement extends Model
{
    use UsesUuid;
    use HasFactory;

    protected $table = 'tier_requirements';

    protected $fillable = [
        'tier_id',
        'name',
        'requirement_type',
        'status',
        'user_created',
        'user_updated',
    ];
}

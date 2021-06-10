<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TierApprovalComment extends Model
{
    use HasFactory, SoftDeletes, UsesUuid;

    protected $table = 'tier_approval_comments';

    protected $fillable = [
        "tier_approval_id",
        "remarks",
        "user_created",
        "user_updated",
    ];
}

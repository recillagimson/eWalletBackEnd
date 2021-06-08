<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSelfiePhoto extends Model
{
    use HasFactory, UsesUuid, SoftDeletes;

    protected $table = 'user_selfie_photos';

    protected $fillable = [
        "tier_approval_id",
        "user_account_id",
        "photo_location",
        "status",
        "remarks",
        "reviewed_by",
        "reviewed_date",
        "user_created",
        "user_updated",
    ];
}

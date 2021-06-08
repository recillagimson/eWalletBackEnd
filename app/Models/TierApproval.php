<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TierApproval extends Model
{
    use HasFactory, UsesUuid, SoftDeletes;

    protected $fillable = [
        "user_account_id",
        "request_tier_id",
        "status",
        "remarks",
        "user_created",
        "user_updated",
        "transaction_number",
        "approved_date",
        "declined_date",
    ];

    public function id_photos() {
        return $this->hasMany(UserPhoto::class, 'tier_approval_id', 'id');
    }

    public function selfie_photos() {
        return $this->hasMany(UserSelfiePhoto::class, 'tier_approval_id', 'id');
    }
}

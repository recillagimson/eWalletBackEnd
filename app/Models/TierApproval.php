<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        "approved_by",
        "approved_date",
        "declined_by",
        "declined_date",
    ];

    protected $appends = [
        'manila_time_created_at',
        'manila_time_approved_at',
        'manila_time_declined_at',
    ];

    public function id_photos() {
        return $this->hasMany(UserPhoto::class, 'tier_approval_id', 'id');
    }

    public function selfie_photos() {
        return $this->hasMany(UserSelfiePhoto::class, 'tier_approval_id', 'id');
    }

    public function user_account() {
        return $this->hasOne(UserAccount::class, 'id', 'user_account_id');
    }

    public function user_detail() {
        return $this->hasOne(UserDetail::class, 'user_account_id', 'user_account_id');
    }

    public function getManilaTimeCreatedAtAttribute() {
        if($this && $this->created_at) {
            return Carbon::parse($this->created_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A');
        }
        return null;
    }

    public function getManilaTimeApprovedAtAttribute() {
        if($this && $this->approved_date) {
            return Carbon::parse($this->approved_date)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A');
        }
        return null;
    }

    public function getManilaTimeDeclinedAtAttribute() {
        if($this && $this->declined_date) {
            return Carbon::parse($this->declined_date)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A');
        }
        return null;
    }
}

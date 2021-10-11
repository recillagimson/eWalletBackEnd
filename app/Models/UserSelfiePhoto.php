<?php

namespace App\Models;

use App\Traits\UsesUuid;
use App\Traits\HasS3Links;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserSelfiePhoto extends Model
{
    use HasFactory, UsesUuid, SoftDeletes, HasS3Links;

    protected $table = 'user_selfie_photos';

    protected $appends = ['selfie_link'];

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
    
    public function getSelfieLinkAttribute() {
        return $this->getTempUrl($this->photo_location, Carbon::now()->addHour()->format('Y-m-d H:i:s'));
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\UsesUuid;
use App\Traits\HasS3Links;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserPhoto extends Model
{
    use HasFactory, SoftDeletes, UsesUuid, HasS3Links;

    protected $table = 'user_id_photos';

    protected $fillable = [
        'id',
        'id_number',
        'user_account_id',
        'id_type_id',
        'old_id_type',
        'photo_location',
        'approval_status',
        'reviewed_by',
        'user_created',
        'user_updated',
        'tier_approval_id',
        'remarks',
        'reviewed_date',
    ];

    public function getAvatarLinkAttribute() {
        return $this->getTempUrl($this->avatar_location, Carbon::now()->addHour()->format('Y-m-d H:i:s'));
    }
}

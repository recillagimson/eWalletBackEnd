<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\UsesUuid;
use App\Traits\HasS3Links;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\hasOneThrough;

class UserPhoto extends Model
{
    use HasFactory, SoftDeletes, UsesUuid, HasS3Links;

    protected $table = 'user_id_photos';

    protected $appends = ['id_photo_link'];

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

    public function getIdPhotoLinkAttribute() {
        return $this->getTempUrl($this->photo_location, Carbon::now()->addHour()->format('Y-m-d H:i:s'));
    }

    public function id_type() {
        return $this->hasOne(IdType::class, 'id', 'id_type_id');
    }

    /**
     * Get all of the reviewer for the UserPhoto
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOneThrough
     */
    public function reviewer(): hasOneThrough
    {
        return $this->hasOneThrough(UserDetail::class, UserAccount::class , 'id', 'user_account_id', 'reviewed_by', 'id');
    }
}

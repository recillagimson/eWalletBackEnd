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

/**
 * App\Models\UserPhoto
 *
 * @property string $id
 * @property string|null $tier_approval_id
 * @property string|null $id_number
 * @property string $user_account_id
 * @property string $id_type_id
 * @property string|null $old_type_id
 * @property string $photo_location
 * @property string $approval_status
 * @property string|null $remarks
 * @property string|null $reviewed_by
 * @property string|null $reviewed_date
 * @property string|null $user_created
 * @property string|null $user_updated
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $id_photo_link
 * @property-read \App\Models\IdType|null $id_type
 * @property-read \App\Models\UserDetail|null $reviewer
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto newQuery()
 * @method static \Illuminate\Database\Query\Builder|UserPhoto onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereApprovalStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereIdNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereIdTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereOldTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto wherePhotoLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereReviewedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereReviewedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereTierApprovalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereUserUpdated($value)
 * @method static \Illuminate\Database\Query\Builder|UserPhoto withTrashed()
 * @method static \Illuminate\Database\Query\Builder|UserPhoto withoutTrashed()
 * @mixin \Eloquent
 */
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

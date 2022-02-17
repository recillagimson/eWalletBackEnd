<?php

namespace App\Models;

use App\Traits\UsesUuid;
use App\Traits\HasS3Links;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\UserSelfiePhoto
 *
 * @property string $id
 * @property string|null $tier_approval_id
 * @property string $user_account_id
 * @property string $photo_location
 * @property string $status
 * @property string|null $remarks
 * @property string|null $reviewed_by
 * @property string|null $reviewed_date
 * @property string $user_created
 * @property string $user_updated
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read mixed $selfie_link
 * @property-read \App\Models\UserDetail|null $reviewer
 * @method static \Illuminate\Database\Eloquent\Builder|UserSelfiePhoto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserSelfiePhoto newQuery()
 * @method static \Illuminate\Database\Query\Builder|UserSelfiePhoto onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UserSelfiePhoto query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserSelfiePhoto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSelfiePhoto whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSelfiePhoto whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSelfiePhoto wherePhotoLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSelfiePhoto whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSelfiePhoto whereReviewedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSelfiePhoto whereReviewedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSelfiePhoto whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSelfiePhoto whereTierApprovalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSelfiePhoto whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSelfiePhoto whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSelfiePhoto whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSelfiePhoto whereUserUpdated($value)
 * @method static \Illuminate\Database\Query\Builder|UserSelfiePhoto withTrashed()
 * @method static \Illuminate\Database\Query\Builder|UserSelfiePhoto withoutTrashed()
 * @mixin \Eloquent
 */
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

    /**
     * Get all of the reviewer for the UserPhoto
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOneThrough
     */
    public function reviewer()
    {
        return $this->hasOneThrough(UserDetail::class, UserAccount::class , 'id', 'user_account_id', 'reviewed_by', 'id');
    }
}

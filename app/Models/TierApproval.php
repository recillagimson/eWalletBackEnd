<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\TierApproval
 *
 * @property string $id
 * @property string|null $transaction_number
 * @property string $user_account_id
 * @property string $request_tier_id
 * @property string $status PENDING,APPROVED,REJECTED
 * @property string|null $remarks
 * @property string|null $declined_by
 * @property string|null $declined_date
 * @property string|null $approved_by
 * @property string|null $approved_date
 * @property string|null $verified_by
 * @property string $user_created
 * @property string $user_updated
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $manila_time_approved_at
 * @property-read mixed $manila_time_created_at
 * @property-read mixed $manila_time_declined_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserPhoto[] $id_photos
 * @property-read int|null $id_photos_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserSelfiePhoto[] $selfie_photos
 * @property-read int|null $selfie_photos_count
 * @property-read \App\Models\UserAccount|null $user_account
 * @property-read \App\Models\UserDetail|null $user_detail
 * @method static \Illuminate\Database\Eloquent\Builder|TierApproval newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TierApproval newQuery()
 * @method static \Illuminate\Database\Query\Builder|TierApproval onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TierApproval query()
 * @method static \Illuminate\Database\Eloquent\Builder|TierApproval whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TierApproval whereApprovedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TierApproval whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TierApproval whereDeclinedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TierApproval whereDeclinedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TierApproval whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TierApproval whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TierApproval whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TierApproval whereRequestTierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TierApproval whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TierApproval whereTransactionNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TierApproval whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TierApproval whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TierApproval whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TierApproval whereUserUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TierApproval whereVerifiedBy($value)
 * @method static \Illuminate\Database\Query\Builder|TierApproval withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TierApproval withoutTrashed()
 * @mixin \Eloquent
 */
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

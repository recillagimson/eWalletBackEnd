<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\TierApprovalComment
 *
 * @property string $id
 * @property string $tier_approval_id
 * @property string $remarks
 * @property string $user_created
 * @property string $user_updated
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|TierApprovalComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TierApprovalComment newQuery()
 * @method static \Illuminate\Database\Query\Builder|TierApprovalComment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TierApprovalComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|TierApprovalComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TierApprovalComment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TierApprovalComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TierApprovalComment whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TierApprovalComment whereTierApprovalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TierApprovalComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TierApprovalComment whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TierApprovalComment whereUserUpdated($value)
 * @method static \Illuminate\Database\Query\Builder|TierApprovalComment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TierApprovalComment withoutTrashed()
 * @mixin \Eloquent
 */
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

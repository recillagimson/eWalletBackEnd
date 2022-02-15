<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\IdType
 *
 * @property string $id
 * @property string $type
 * @property string $description
 * @property int $is_primary
 * @property int $status
 * @property int $is_ekyc
 * @property string $is_with_expiration
 * @property int $is_full_name
 * @property int $is_face_match_required
 * @property string $user_created
 * @property string|null $user_updated
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|IdType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IdType newQuery()
 * @method static \Illuminate\Database\Query\Builder|IdType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|IdType query()
 * @method static \Illuminate\Database\Eloquent\Builder|IdType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IdType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IdType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IdType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IdType whereIsEkyc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IdType whereIsFaceMatchRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IdType whereIsFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IdType whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IdType whereIsWithExpiration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IdType whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IdType whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IdType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IdType whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IdType whereUserUpdated($value)
 * @method static \Illuminate\Database\Query\Builder|IdType withTrashed()
 * @method static \Illuminate\Database\Query\Builder|IdType withoutTrashed()
 * @mixin \Eloquent
 */
class IdType extends Model
{
    use HasFactory, SoftDeletes;
    use UsesUuid;

    protected $table = 'id_types';

    protected $fillable =[
        'type',
        'description',
        'swirecommended',
        'is_ekyc',
        'is_full_name',
        'is_with_expiration',
        'status',
        'user_created',
        'user_updated'
    ];
}

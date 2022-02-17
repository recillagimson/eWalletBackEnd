<?php

namespace App\Models\UserUtilities;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserUtilities\NatureOfWork
 *
 * @property string $id
 * @property string $description
 * @property int $status
 * @property string $user_created
 * @property string|null $user_updated
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|NatureOfWork newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NatureOfWork newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NatureOfWork query()
 * @method static \Illuminate\Database\Eloquent\Builder|NatureOfWork whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NatureOfWork whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NatureOfWork whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NatureOfWork whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NatureOfWork whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NatureOfWork whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NatureOfWork whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NatureOfWork whereUserUpdated($value)
 * @mixin \Eloquent
 */
class NatureOfWork extends Model
{
    use UsesUuid, HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "natures_of_work";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "description",
        "status",
        "user_created",
        "user_updated"
    ];
}

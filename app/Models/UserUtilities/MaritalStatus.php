<?php

namespace App\Models\UserUtilities;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserUtilities\MaritalStatus
 *
 * @property string $id
 * @property string $description
 * @property string $legend
 * @property int $status
 * @property string $user_created
 * @property string|null $user_updated
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MaritalStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MaritalStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MaritalStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|MaritalStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaritalStatus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaritalStatus whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaritalStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaritalStatus whereLegend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaritalStatus whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaritalStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaritalStatus whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaritalStatus whereUserUpdated($value)
 * @mixin \Eloquent
 */
class MaritalStatus extends Model
{
    use UsesUuid, HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "marital_status";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "description",
        "legend",
        "status",
        "user_created",
        "user_updated"
    ];
}

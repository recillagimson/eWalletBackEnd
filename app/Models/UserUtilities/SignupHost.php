<?php

namespace App\Models\UserUtilities;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserUtilities\SignupHost
 *
 * @property string $id
 * @property string $description
 * @property int $status
 * @property string $user_created
 * @property string|null $user_updated
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SignupHost newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SignupHost newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SignupHost query()
 * @method static \Illuminate\Database\Eloquent\Builder|SignupHost whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SignupHost whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SignupHost whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SignupHost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SignupHost whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SignupHost whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SignupHost whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SignupHost whereUserUpdated($value)
 * @mixin \Eloquent
 */
class SignupHost extends Model
{
    use UsesUuid, HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "signup_hosts";

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

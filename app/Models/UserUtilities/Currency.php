<?php

namespace App\Models\UserUtilities;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserUtilities\Currency
 *
 * @property string $id
 * @property string $description
 * @property string $code
 * @property int $status
 * @property string $user_created
 * @property string|null $user_updated
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Currency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Currency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Currency query()
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereUserUpdated($value)
 * @mixin \Eloquent
 */
class Currency extends Model
{
    use UsesUuid, HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "currencies";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "description",
        "code",
        "status",
        "user_created",
        "user_updated"
    ];
}

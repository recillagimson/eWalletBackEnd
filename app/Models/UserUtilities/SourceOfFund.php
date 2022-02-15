<?php

namespace App\Models\UserUtilities;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserUtilities\SourceOfFund
 *
 * @property string $id
 * @property string $description
 * @property int $status
 * @property string $user_created
 * @property string|null $user_updated
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SourceOfFund newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SourceOfFund newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SourceOfFund query()
 * @method static \Illuminate\Database\Eloquent\Builder|SourceOfFund whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SourceOfFund whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SourceOfFund whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SourceOfFund whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SourceOfFund whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SourceOfFund whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SourceOfFund whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SourceOfFund whereUserUpdated($value)
 * @mixin \Eloquent
 */
class SourceOfFund extends Model
{
    use UsesUuid, HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "source_of_funds";

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

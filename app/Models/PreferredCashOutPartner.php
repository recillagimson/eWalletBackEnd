<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PreferredCashOutPartner
 *
 * @property string $id
 * @property string $description
 * @property string $status
 * @property string $user_created
 * @property string $user_updated
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|PreferredCashOutPartner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PreferredCashOutPartner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PreferredCashOutPartner query()
 * @method static \Illuminate\Database\Eloquent\Builder|PreferredCashOutPartner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreferredCashOutPartner whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreferredCashOutPartner whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreferredCashOutPartner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreferredCashOutPartner whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreferredCashOutPartner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreferredCashOutPartner whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PreferredCashOutPartner whereUserUpdated($value)
 * @mixin \Eloquent
 */
class PreferredCashOutPartner extends Model
{
    use HasFactory, UsesUuid;

    protected $fillable = [
        "description",
        "status",
        "user_created",
        "user_updated",
    ];
}

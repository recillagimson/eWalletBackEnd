<?php

namespace App\Models\UserKeys;

use App\Traits\UsesUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserKeys\PinCodeHistory
 *
 * @property string $id
 * @property string $user_account_id
 * @property string $key
 * @property int $expired
 * @property string $user_created
 * @property string|null $user_updated
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int $key_age
 * @method static \Illuminate\Database\Eloquent\Builder|PinCodeHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PinCodeHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PinCodeHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|PinCodeHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PinCodeHistory whereExpired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PinCodeHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PinCodeHistory whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PinCodeHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PinCodeHistory whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PinCodeHistory whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PinCodeHistory whereUserUpdated($value)
 * @mixin \Eloquent
 */
class PinCodeHistory extends Model implements IUserKeyModel
{
    use UsesUuid, HasFactory;

    protected $fillable = [
        'user_account_id',
        'key',
        'user_created',
        'user_updated'
    ];

    public function getKeyAgeAttribute(): int
    {
        return $this->created_at->diffInDays(Carbon::now());
    }

    public function isAboutToExpire(int $daysToNotify = 15, int $maxAge = 60): bool
    {
        $remainingAge = $maxAge - $this->key_age;
        if ($remainingAge <= $daysToNotify) {
            return true;
        }

        return false;
    }

    public function isAtMinimumAge(int $minAge = 1): bool
    {
        $currentAge = $this->key_age;
        if ($currentAge < $minAge) return false;
        return true;
    }

}

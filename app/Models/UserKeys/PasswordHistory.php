<?php

namespace App\Models\UserKeys;

use App\Traits\UsesUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserKeys\PasswordHistory
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
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory whereExpired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory whereUserUpdated($value)
 * @mixin \Eloquent
 */
class PasswordHistory extends Model implements IUserKeyModel
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
        if($remainingAge <= $daysToNotify)
        {
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

<?php

namespace App\Models\UserKeys;

use App\Traits\UsesUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

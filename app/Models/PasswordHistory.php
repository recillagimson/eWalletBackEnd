<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordHistory extends Model
{
    use UsesUuid, HasFactory;

    protected $fillable = [
        'user_account_id',
        'password',
        'user_created',
        'user_updated'
    ];

    public function getPasswordAgeAttribute(): int
    {
        return $this->created_at->diffInDays(Carbon::now());
    }

    public function isAboutToExpire(int $daysToNotify = 15, int $maxAge = 60): bool
    {
        $remainingAge = $maxAge - $this->password_age;
        if($remainingAge <= $daysToNotify)
        {
            return true;
        }

        return false;
    }

    public function isAtMinimumAge(int $minAge): bool
    {
        $currentAge = $this->password_age;
        if($currentAge < $minAge) return false;
        return true;
    }


}

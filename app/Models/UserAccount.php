<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class UserAccount extends Authenticatable
{
    use UsesUuid;
    use SoftDeletes, HasApiTokens, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'mobile_number',
        'password',
        'pin_code',
        'verified',
        'is_admin',
        'tier_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'pin_code',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_failed_attempt' => 'datetime',
    ];


    public function updateLockout(int $maxLoginAttempts)
    {
        $this->login_failed_attempts += 1;
        $this->last_failed_attempt = Carbon::now();
        $this->is_lockout = $this->login_failed_attempts >= $maxLoginAttempts;
        $this->save();
    }

    public function resetLoginAttempts(int $daysToReset)
    {
        if($this->last_failed_attempt)
        {
            $diffInDays = $this->last_failed_attempt->diffInDays(Carbon::now());
            if($diffInDays >= $daysToReset)
            {
                $this->login_failed_attempts = 0;
                $this->save();
            }
        }
    }

    public function deleteAllTokens()
    {
        $this->tokens()->delete();
    }

    public function deleteTokensByName(string $tokenName)
    {
        $this->tokens()->where('name', $tokenName)->delete();
    }

    public function tier() {
        return $this->hasOne(Tier::class, 'id', 'tier_id');
    }

}

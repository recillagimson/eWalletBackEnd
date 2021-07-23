<?php

namespace App\Models;

use App\Models\Admin\Role;
use App\Models\UserUtilities\UserDetail;
use App\Traits\HasS3Links;
use App\Traits\UsesUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class UserAccount extends Authenticatable
{
    use UsesUuid, HasS3Links;
    use SoftDeletes, HasApiTokens, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_number',
        'email',
        'mobile_number',
        'password',
        'pin_code',
        'verified',
        'is_admin',
        'tier_id',
        'user_created',
        'user_updated',
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
        'last_login' => 'datetime',
    ];

    public function tier(): HasOne
    {
        return $this->hasOne(Tier::class, 'id', 'tier_id');
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserDetail::class, 'user_account_id', 'id');
    }

    public function balanceInfo(): HasOne
    {
        return $this->hasOne(UserBalanceInfo::class, 'user_account_id', 'id');
    }

    public function userDetail(): HasOne
    {
        return $this->hasOne(UserDetail::class, 'user_account_id', 'id');
    }

    public function verificationToken(): HasOne
    {
        return $this->hasOne(AdminUserVerifyToken::class);
    }

    public function tierApprovals(): HasMany
    {
        return $this->HasMany(TierApproval::class, 'user_account_id', 'id');
    }

    public function updateLockout(int $maxLoginAttempts)
    {
        $this->login_failed_attempts += 1;
        $this->last_failed_attempt = Carbon::now();
        $this->is_lockout = $this->login_failed_attempts >= $maxLoginAttempts;
        $this->save();
    }

    public function resetLoginAttempts(int $daysToReset, bool $resetLockout = false)
    {
        if ($resetLockout) {
            $this->login_failed_attempts = 0;
            $this->is_lockout = false;
            $this->save();
            return;
        }

        if ($this->last_failed_attempt) {
            $diffInDays = $this->last_failed_attempt->diffInDays(Carbon::now());
            if ($diffInDays >= $daysToReset) {
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

    public function roles(): HasManyThrough
    {
        return $this->hasManyThrough(Role::class, UserRole::class, 'user_account_id', 'id', 'id', 'role_id');
    }

    public function user_balance_info(): HasOne
    {
        return $this->hasOne(UserBalanceInfo::class, 'user_account_id', 'id');
    }

    public function toggleActivation()
    {
        $this->is_active = !$this->is_active;
        $this->save();
    }

    public function toggleLockout()
    {
        $this->is_lockout = !$this->is_lockout;
        $this->login_failed_attempts = 0;
        $this->last_failed_attempt = null;
        $this->save();
    }
}

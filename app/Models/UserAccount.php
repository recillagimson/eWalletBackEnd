<?php

namespace App\Models;

use App\Models\Admin\Role;
use App\Models\UserUtilities\UserDetail;
use App\Traits\HasS3Links;
use App\Traits\UsesUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\UserAccount
 *
 * @property string $id
 * @property string|null $entity_id
 * @property string|null $merchant_id
 * @property string|null $username
 * @property string|null $email
 * @property string|null $mobile_number
 * @property string $password
 * @property int|null $is_merchant
 * @property string|null $merchant_type_id
 * @property int $is_admin
 * @property string|null $status
 * @property string|null $old_creation_date_time_from_v3_DB
 * @property string|null $pin_code
 * @property string|null $tier_id
 * @property string|null $user_created
 * @property string|null $user_updated
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $expires_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $verified
 * @property int $is_lockout
 * @property int $login_failed_attempts
 * @property \Illuminate\Support\Carbon|null $last_failed_attempt
 * @property int $is_active
 * @property string $accept_tac_consent_date
 * @property int|null $is_accept_tac_consent
 * @property string $accept_dpa_consent_date
 * @property int|null $is_accept_dpa_consent
 * @property string|null $account_number
 * @property string|null $merchant_account_id
 * @property \Illuminate\Support\Carbon|null $last_login
 * @property string|null $rsbsa_number
 * @property int $is_onboarder
 * @property int $otp_enabled
 * @property int|null $is_login_email
 * @property int $is_lockout_admin
 * @property-read \App\Models\UserBalanceInfo|null $balanceInfo
 * @property-read string $manila_time_created_at
 * @property-read \App\Models\TierApproval|null $lastTierApproval
 * @property-read \App\Models\MerchantAccount|null $merchant_account
 * @property-read UserDetail|null $profile
 * @property-read \Illuminate\Database\Eloquent\Collection|Role[] $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\Tier|null $tier
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TierApproval[] $tierApprovals
 * @property-read int|null $tier_approvals_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @property-read UserDetail|null $userDetail
 * @property-read \App\Models\UserBalanceInfo|null $user_balance_info
 * @property-read \App\Models\AdminUserVerifyToken|null $verificationToken
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount newQuery()
 * @method static \Illuminate\Database\Query\Builder|UserAccount onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereAcceptDpaConsentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereAcceptTacConsentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereIsAcceptDpaConsent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereIsAcceptTacConsent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereIsLockout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereIsLockoutAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereIsLoginEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereIsMerchant($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereIsOnboarder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereLastFailedAttempt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereLoginFailedAttempts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereMerchantAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereMerchantTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereOldCreationDateTimeFromV3DB($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereOtpEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount wherePinCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereRsbsaNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereTierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereUserUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccount whereVerified($value)
 * @method static \Illuminate\Database\Query\Builder|UserAccount withTrashed()
 * @method static \Illuminate\Database\Query\Builder|UserAccount withoutTrashed()
 * @mixin \Eloquent
 */
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
        'rsbsa_number',
        'merchant_account_id'
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

    protected $appends = ['manila_time_created_at'];

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

    public function merchant_account(): HasOne
    {
        return $this->hasOne(MerchantAccount::class, 'id', 'merchant_account_id');
    }

    public function lastTierApproval() {
        return $this->hasOne(TierApproval::class, 'user_account_id', 'id')
        ->orderBy('created_at', 'DESC');
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

    public function getManilaTimeCreatedAtAttribute(): string
    {
        return Carbon::parse($this->created_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A');
    }
}

<?php

namespace App\Models\UBP;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UBP\UbpAccountToken
 *
 * @property string $id
 * @property string $user_account_id
 * @property string $token_type
 * @property string $access_token
 * @property string $metadata
 * @property \Illuminate\Support\Carbon $expires_in
 * @property \Illuminate\Support\Carbon $consented_on
 * @property string $scope
 * @property string $refresh_token
 * @property \Illuminate\Support\Carbon $refresh_token_expiration
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UbpAccountToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UbpAccountToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UbpAccountToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|UbpAccountToken whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UbpAccountToken whereConsentedOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UbpAccountToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UbpAccountToken whereExpiresIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UbpAccountToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UbpAccountToken whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UbpAccountToken whereRefreshToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UbpAccountToken whereRefreshTokenExpiration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UbpAccountToken whereScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UbpAccountToken whereTokenType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UbpAccountToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UbpAccountToken whereUserAccountId($value)
 * @mixin \Eloquent
 */
class UbpAccountToken extends Model
{
    use UsesUuid, HasFactory;

    protected $fillable = [
        'user_account_id',
        'token_type',
        'access_token',
        'metadata',
        'expires_in',
        'consented_on',
        'scope',
        'refresh_token',
        'refresh_token_expiration'
    ];

    protected $casts = [
        'expires_in' => 'datetime',
        'consented_on' => 'datetime',
        'refresh_token_expiration' => 'datetime',
    ];

}

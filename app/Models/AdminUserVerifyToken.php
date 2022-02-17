<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AdminUserVerifyToken
 *
 * @property string $user_account_id
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUserVerifyToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUserVerifyToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUserVerifyToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUserVerifyToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUserVerifyToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUserVerifyToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUserVerifyToken whereUserAccountId($value)
 * @mixin \Eloquent
 */
class AdminUserVerifyToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'token'
    ];
}

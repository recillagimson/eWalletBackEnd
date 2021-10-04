<?php

namespace App\Models\UBP;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

}

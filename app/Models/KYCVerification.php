<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KYCVerification extends Model
{
    use HasFactory, UsesUuid;

    protected $table = 'kyc_verifications';

    protected $fillable = [
        'user_account_id',
        'request_id',
        'application_id',
        'hv_response',
        'hv_result',
        'status',
    ];
}

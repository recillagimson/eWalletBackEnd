<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaceAuthTransaction extends Model
{
    use HasFactory, UsesUuid;

    protected $table = 'face_auth_transactions';

    protected $fillable = [
        "transaction_id",
        "rsbsa_number",
        "user_account_id",
        "response",
    ];
}

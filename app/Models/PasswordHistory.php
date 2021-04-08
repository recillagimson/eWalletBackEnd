<?php

namespace App\Models;

use App\Traits\UsesUuid;
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


}

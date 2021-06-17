<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRole extends Model
{
    use HasFactory,UsesUuid, SoftDeletes;

    protected $table = 'user_account_roles';

    protected $fillable = [
        'user_account_id',
        'role_id'
    ];
}

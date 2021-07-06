<?php

namespace App\Models;

use App\Traits\UsesUuid;
use App\Models\Admin\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserRole extends Model
{
    use HasFactory,UsesUuid, SoftDeletes;

    protected $table = 'user_account_roles';

    protected $fillable = [
        'user_account_id',
        'role_id'
    ];

    public function role() {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
}

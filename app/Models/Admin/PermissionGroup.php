<?php

namespace App\Models\Admin;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermissionGroup extends Model
{
    use HasFactory, SoftDeletes, UsesUuid;

    protected $fillable = [
        "role_id",
        "permission_id"
    ];

    public function permissions() {
        return $this->hasMany(Permission::class, 'permission_group_id', 'id');
    }
}

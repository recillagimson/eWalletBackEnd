<?php

namespace App\Models\Admin;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Admin\RolePermission
 *
 * @property string $id
 * @property string $role_id
 * @property string $permission_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|RolePermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RolePermission newQuery()
 * @method static \Illuminate\Database\Query\Builder|RolePermission onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RolePermission query()
 * @method static \Illuminate\Database\Eloquent\Builder|RolePermission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RolePermission whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RolePermission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RolePermission wherePermissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RolePermission whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RolePermission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|RolePermission withTrashed()
 * @method static \Illuminate\Database\Query\Builder|RolePermission withoutTrashed()
 * @mixin \Eloquent
 */
class RolePermission extends Model
{
    use HasFactory, SoftDeletes, UsesUuid;

    protected $fillable = [
        'role_id',
        'permission_id'
    ];
}

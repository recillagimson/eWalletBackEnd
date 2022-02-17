<?php

namespace App\Models\Admin;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Admin\PermissionGroup
 *
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Admin\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup newQuery()
 * @method static \Illuminate\Database\Query\Builder|PermissionGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|PermissionGroup withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PermissionGroup withoutTrashed()
 * @mixin \Eloquent
 */
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

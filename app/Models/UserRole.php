<?php

namespace App\Models;

use App\Traits\UsesUuid;
use App\Models\Admin\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\UserRole
 *
 * @property string $id
 * @property string $user_account_id
 * @property string $role_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Role|null $role
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole newQuery()
 * @method static \Illuminate\Database\Query\Builder|UserRole onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRole whereUserAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|UserRole withTrashed()
 * @method static \Illuminate\Database\Query\Builder|UserRole withoutTrashed()
 * @mixin \Eloquent
 */
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

<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\WhiteList
 *
 * @property string $id
 * @property string $ip
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|WhiteList newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WhiteList newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WhiteList query()
 * @method static \Illuminate\Database\Eloquent\Builder|WhiteList whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WhiteList whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WhiteList whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WhiteList whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WhiteList whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WhiteList whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class WhiteList extends Model
{
    use HasFactory, UsesUuid;

    protected $table = 'white_lists';
}

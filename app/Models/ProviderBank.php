<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProviderBank
 *
 * @property string $provider
 * @property string $code
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderBank newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderBank newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderBank query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderBank whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderBank whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderBank whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderBank whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderBank whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProviderBank extends Model
{
    use UsesUuid, HasFactory;

    protected $fillable = [
        'provider',
        'code',
        'name'
    ];
}

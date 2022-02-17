<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Admin\ForeignExchangeRate
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property float $rate
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ForeignExchangeRate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForeignExchangeRate newQuery()
 * @method static \Illuminate\Database\Query\Builder|ForeignExchangeRate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ForeignExchangeRate query()
 * @method static \Illuminate\Database\Eloquent\Builder|ForeignExchangeRate whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForeignExchangeRate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForeignExchangeRate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForeignExchangeRate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForeignExchangeRate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForeignExchangeRate whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForeignExchangeRate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ForeignExchangeRate withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ForeignExchangeRate withoutTrashed()
 * @mixin \Eloquent
 */
class ForeignExchangeRate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'code',
        'rate'
    ];

}

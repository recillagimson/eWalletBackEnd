<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserAccountNumber
 *
 * @property string $id
 * @property \Illuminate\Support\Carbon $account_date
 * @property int $counter
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccountNumber newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccountNumber newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccountNumber query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccountNumber whereAccountDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccountNumber whereCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccountNumber whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccountNumber whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccountNumber whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UserAccountNumber extends Model
{
    use UsesUuid, HasFactory;

    protected $fillable = [
        'account_date',
        'counter',
    ];

    protected $casts = [
        'account_date' => 'datetime',
    ];
}

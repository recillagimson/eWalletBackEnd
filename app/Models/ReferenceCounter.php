<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ReferenceCounter
 *
 * @property int $id
 * @property string $code
 * @property int $counter
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceCounter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceCounter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceCounter query()
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceCounter whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceCounter whereCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceCounter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceCounter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferenceCounter whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ReferenceCounter extends Model
{
    use HasFactory;
}

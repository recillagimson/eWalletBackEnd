<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Barangay
 *
 * @property int $id
 * @property string $name
 * @property string $municipality_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Barangay newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Barangay newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Barangay query()
 * @method static \Illuminate\Database\Eloquent\Builder|Barangay whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barangay whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barangay whereMunicipalityCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barangay whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barangay whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Barangay extends Model
{
    use HasFactory;
}

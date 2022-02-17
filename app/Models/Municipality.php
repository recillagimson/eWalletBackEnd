<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Municipality
 *
 * @property int $id
 * @property string $name
 * @property string $province_code
 * @property string $municipality_code
 * @property string $zip_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Municipality newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Municipality newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Municipality query()
 * @method static \Illuminate\Database\Eloquent\Builder|Municipality whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Municipality whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Municipality whereMunicipalityCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Municipality whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Municipality whereProvinceCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Municipality whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Municipality whereZipCode($value)
 * @mixin \Eloquent
 */
class Municipality extends Model
{
    use HasFactory;
}

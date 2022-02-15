<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

/**
 * App\Models\FarmerImport
 *
 * @property string|null $id
 * @property string $filename
 * @property int|null $seq
 * @property string|null $province
 * @property int|null $success
 * @property int|null $fails
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FarmerImport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FarmerImport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FarmerImport query()
 * @method static \Illuminate\Database\Eloquent\Builder|FarmerImport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FarmerImport whereFails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FarmerImport whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FarmerImport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FarmerImport whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FarmerImport whereSeq($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FarmerImport whereSuccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FarmerImport whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FarmerImport extends Model
{
    use HasFactory, UsesUuid;

    protected $table = "farmer_imports";

    protected $fillable = [
        "filename",
        "seq",
        "province",
        "success",
        "fails",
    ];
}

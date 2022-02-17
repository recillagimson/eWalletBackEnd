<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FarmerSubsidy
 *
 * @property string $id
 * @property string $filename
 * @property int $seq
 * @property string $province
 * @property string $success
 * @property string $fails
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|FarmerSubsidy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FarmerSubsidy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FarmerSubsidy query()
 * @method static \Illuminate\Database\Eloquent\Builder|FarmerSubsidy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FarmerSubsidy whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FarmerSubsidy whereFails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FarmerSubsidy whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FarmerSubsidy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FarmerSubsidy whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FarmerSubsidy whereSeq($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FarmerSubsidy whereSuccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FarmerSubsidy whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FarmerSubsidy extends Model
{
    use HasFactory, UsesUuid;

    protected $table = 'farmer_subsidies';

    protected $fillable = [
        'filename',
        'province',
        'seq',
        'filename',
        'success',
        'fails',
    ];
}

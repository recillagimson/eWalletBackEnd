<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MerchantAccount
 *
 * @property string $id
 * @property string $name
 * @property string $type
 * @property string $house_no
 * @property string $city_municipality
 * @property string $province
 * @property string $authorized_representative
 * @property string $company_email
 * @property string $contact_number
 * @property float $merchant_balance
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantAccount whereAuthorizedRepresentative($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantAccount whereCityMunicipality($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantAccount whereCompanyEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantAccount whereContactNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantAccount whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantAccount whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantAccount whereHouseNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantAccount whereMerchantBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantAccount whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantAccount whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantAccount whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantAccount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantAccount whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class MerchantAccount extends Model
{
    use HasFactory, UsesUuid;
    
    protected $table = 'merchant_accounts';

    protected $fillable = [
        'name',
        'type',
        'house_no',
        'city_municipality',
        'province',
        'authorized_representative',
        'company_email',
        'contact_number',
        'merchant_balance',
        'created_by',
        'updated_by',
    ];
}

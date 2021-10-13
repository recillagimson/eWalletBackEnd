<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantAccount extends Model
{
    use HasFactory;
    
    protected $table = 'merchant_accounts';

    protected $fillables = [
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

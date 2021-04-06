<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDetail extends Model
{
    use HasFactory, UsesUuid, SoftDeletes;

    protected $table = 'user_details';
    protected $fillable = [
        'user_account_id',
        'entity_id',
        'lastName',
        'firstname',
        'middlename',
        'name_extension',
        'birthdate',
        'place_of_birth',
        'maritial_status_id',
        'nationality_id',
        'encoded_nationality',
        'occupation',
        'house_no_street',
        'city',
        'provice_state',
        'municipality',
        'country_id',
        'postal_code',
        'nature_of_work_id',
        'encoded_nature_of_work',
        'source_of_fund_id',
        'encoded_source_of_fund',
        'mother_maidenname',
        'currency_id',
        'signup_host_id',
        'verification_status',
        'user_account_status',
        'emergency_lock_status',
        'report_exception_status',
        'user_created',
        'user_updated',
    ];
}

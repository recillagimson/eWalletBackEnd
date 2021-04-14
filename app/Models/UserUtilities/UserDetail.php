<?php

namespace App\Models\UserUtilities;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use UsesUuid, HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "user_details";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "entity_id",
        "user_account_id",
        "title",
        "last_name",
        "first_name",
        "middle_name",
        "name_extension",
        "birth_date",
        "place_of_birth",
        "marital_status_id",
        "nationality_id",
        "encoded_nationality",
        "occupation",
        "house_no_street",
        "city",
        "provice_state",
        "municipality",
        "country_id",
        "postal_code",
        "nature_of_work_id",
        "encoded_nature_of_work",
        "source_of_fund_id",
        "encoded_source_of_fund",
        "mother_maidenname",
        "currency_id",
        "signup_host_id",
        "verification_status",
        "user_account_status",
        "emergency_lock_status",
        "report_exception_status",
        "user_created",
        "user_updated",
    ];

}
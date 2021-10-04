<?php

namespace App\Models\UserUtilities;

use App\Traits\UsesUuid;
use App\Models\UserAccount;
use App\Traits\HasS3Links;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserDetail extends Model
{
    use UsesUuid, HasFactory, HasS3Links;

    protected $appends = [
        'email', 'mobile_number', 'avatar_link'
    ];
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
        "province_state",
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
        "guardian_name",
        "guardian_mobile_number",
        "is_accept_parental_consent",
        "occupation",
        "employer",
        "contact_no",
        "avatar_location",
        "barangay",
        "signature_photo_location",
        'id_number',
        'government_id_type',
        'district',
        'region',
        'sex',
        'no_of_farm_parcel',
        'total_farm_area',
        'name_of_da_personel',
        'da_remarks',
        'is_da_update',
        'preferred_cash_out_partner'
    ];

    public function getUserAccount()
    {
        return $this->hasOne(UserAccount::class, 'id');
    }

    public function user_account()
    {
        return $this->hasOne(UserAccount::class, 'id', 'user_account_id');
    }

    public function getEmailAttribute() {
        return $this->user_account ? $this->user_account->email : "";
    }
    public function getMobileNumberAttribute() {
        return $this->user_account ? $this->user_account->mobile_number : "";
    }

    public function getAvatarLinkAttribute() {
        // return Storage::disk('s3')->temporaryUrl($this->avatar_location, Carbon::now()->addHour(1));
        if(Storage::disk('s3')->exists($this->avatar_location)) {
            return $this->getTempUrl($this->avatar_location, Carbon::now()->addHour(1)->format('Y-m-d H:i:s'));
        }
        return "";
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

}

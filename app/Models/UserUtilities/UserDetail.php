<?php

namespace App\Models\UserUtilities;

use App\Traits\UsesUuid;
use App\Models\UserAccount;
use App\Traits\HasS3Links;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\UserUtilities\UserDetail
 *
 * @property string $id
 * @property string|null $entity_id
 * @property string $user_account_id
 * @property string|null $title
 * @property string $last_name
 * @property string $first_name
 * @property string $middle_name
 * @property string|null $name_extension
 * @property string|null $birth_date
 * @property string|null $place_of_birth
 * @property string|null $marital_status_id
 * @property string|null $nationality_id
 * @property string|null $encoded_nationality
 * @property string|null $occupation
 * @property string|null $house_no_street
 * @property string|null $barangay
 * @property string|null $city
 * @property string|null $province_state
 * @property string|null $municipality
 * @property string|null $country_id
 * @property string|null $postal_code
 * @property string|null $nature_of_work_id
 * @property string|null $encoded_nature_of_work
 * @property string|null $source_of_fund_id
 * @property string|null $encoded_source_of_fund
 * @property string|null $mother_maidenname
 * @property string|null $currency_id
 * @property string|null $signup_host_id
 * @property string|null $selfie_loction
 * @property string|null $avatar_location
 * @property string|null $verification_status
 * @property string|null $user_account_status
 * @property string|null $emergency_lock_status
 * @property string|null $report_exception_status
 * @property string $user_created
 * @property string|null $user_updated
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $guardian_name
 * @property string|null $guardian_mobile_number
 * @property int|null $is_accept_parental_consent
 * @property string|null $contact_no
 * @property string|null $employer
 * @property string|null $signature_photo_location
 * @property float|null $no_of_farm_parcel
 * @property float|null $total_farm_area
 * @property string|null $id_number
 * @property string|null $government_id_type
 * @property string|null $district
 * @property string|null $region
 * @property string|null $sex
 * @property string|null $name_of_da_personel
 * @property string|null $da_remarks
 * @property int|null $is_da_update
 * @property string|null $preferred_cash_out_partner
 * @property-read mixed $avatar_link
 * @property-read mixed $email
 * @property-read string $full_name
 * @property-read mixed $mobile_number
 * @property-read UserAccount|null $user_account
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereAvatarLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereBarangay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereContactNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereDaRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereEmergencyLockStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereEmployer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereEncodedNationality($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereEncodedNatureOfWork($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereEncodedSourceOfFund($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereGovernmentIdType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereGuardianMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereGuardianName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereHouseNoStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereIdNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereIsAcceptParentalConsent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereIsDaUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereMaritalStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereMotherMaidenname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereMunicipality($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereNameExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereNameOfDaPersonel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereNationalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereNatureOfWorkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereNoOfFarmParcel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereOccupation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail wherePlaceOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail wherePreferredCashOutPartner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereProvinceState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereReportExceptionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereSelfieLoction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereSignaturePhotoLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereSignupHostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereSourceOfFundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereTotalFarmArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereUserAccountStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereUserUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDetail whereVerificationStatus($value)
 * @mixin \Eloquent
 */
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

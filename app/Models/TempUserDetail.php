<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\TempUserDetail
 *
 * @property string $id
 * @property string $transaction_number
 * @property string $user_account_id
 * @property string $last_name
 * @property string $first_name
 * @property string $middle_name
 * @property string|null $name_extension
 * @property string $nationality_id
 * @property string|null $encoded_nationality
 * @property string $birth_date
 * @property string $house_no_street
 * @property string $province_state
 * @property string $city
 * @property string $postal_code
 * @property string|null $country_id
 * @property string $place_of_birth
 * @property string $mother_maidenname
 * @property string $marital_status_id
 * @property string $occupation
 * @property string $nature_of_work_id
 * @property string|null $encoded_nature_of_work
 * @property string $source_of_fund_id
 * @property string|null $encoded_source_of_fund
 * @property string $employer
 * @property string|null $mobile_number
 * @property string|null $email
 * @property string $status
 * @property string|null $remarks
 * @property string $reviewed_by
 * @property \Illuminate\Support\Carbon $reviewed_date
 * @property string|null $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_date
 * @property string|null $declined_by
 * @property \Illuminate\Support\Carbon|null $declined_date
 * @property string $user_created
 * @property string $user_updated
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $guardian_name
 * @property string|null $guardian_mobile_number
 * @property int|null $is_accept_parental_consent
 * @property string|null $contact_no
 * @property-read \App\Models\TierApproval|null $latestTierApproval
 * @property-read \App\Models\UserAccount $user
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail newQuery()
 * @method static \Illuminate\Database\Query\Builder|TempUserDetail onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereApprovedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereContactNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereDeclinedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereDeclinedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereEmployer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereEncodedNationality($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereEncodedNatureOfWork($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereEncodedSourceOfFund($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereGuardianMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereGuardianName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereHouseNoStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereIsAcceptParentalConsent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereMaritalStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereMotherMaidenname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereNameExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereNationalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereNatureOfWorkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereOccupation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail wherePlaceOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereProvinceState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereReviewedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereReviewedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereSourceOfFundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereTransactionNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TempUserDetail whereUserUpdated($value)
 * @method static \Illuminate\Database\Query\Builder|TempUserDetail withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TempUserDetail withoutTrashed()
 * @mixin \Eloquent
 */
class TempUserDetail extends Model
{
    use HasFactory, UsesUuid, SoftDeletes;

    protected $fillable = [
        "transaction_number",
        "user_account_id",
        "last_name",
        "first_name",
        "middle_name",
        "name_extension",
        "nationality_id",
        "encoded_nationality",
        "birth_date",
        "house_no_street",
        "province_state",
        "city",
        "postal_code",
        "country_id",
        "place_of_birth",
        "mother_maidenname",
        "marital_status_id",
        "occupation",
        "nature_of_work_id",
        "encoded_nature_of_work",
        "source_of_fund_id",
        "encoded_source_of_fund",
        "employer",
        "mobile_number",
        "email",
        "status",
        "remarks",
        "reviewed_by",
        "reviewed_date",
        "approved_by",
        "approved_date",
        "declined_by",
        "declined_date",
        "user_created",
        "user_updated",
        "guardian_name",
        "guardian_mobile_number",
        "is_accept_parental_consent",
        "contact_no",
    ];

    protected $dates = [
        "reviewed_date",
        "approved_date",
        "declined_date"
    ];

    /**
     * Get the user that owns the TempUserDetail
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'user_account_id', 'id');
    }

    public function latestTierApproval()
    {
        return $this->hasOne(TierApproval::class, 'user_account_id', 'user_account_id')
            ->where('status', "!=", "REJECTED")
            ->orderBy('created_at', 'DESC');
    }
}

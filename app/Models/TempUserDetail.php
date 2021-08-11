<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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

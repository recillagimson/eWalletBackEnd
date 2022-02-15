<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\InReceiveFromDBP
 *
 * @property string $id
 * @property string $user_account_id
 * @property string $reference_number
 * @property string $total_amount
 * @property string $transaction_date
 * @property string $transaction_category_id
 * @property string $transaction_remarks
 * @property string $file_name
 * @property string $status
 * @property string $user_created
 * @property string $user_updated
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $funding_currency
 * @property string|null $remittance_date
 * @property string|null $service_code
 * @property string|null $outlet_name
 * @property string|null $beneficiary_name_1
 * @property string|null $beneficiary_name_2
 * @property string|null $beneficiary_name_3
 * @property string|null $beneficiary_address_1
 * @property string|null $beneficiary_address_2
 * @property string|null $beneficiary_address_3
 * @property string|null $mobile_number
 * @property string|null $message
 * @property string|null $remitter_name_1
 * @property string|null $remitter_name_2
 * @property string|null $remitter_address_1
 * @property string|null $remitter_address_2
 * @property-read \App\Models\UserAccount|null $sender
 * @property-read \App\Models\UserDetail|null $sender_details
 * @property-read \App\Models\UserAccount|null $user_account
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP newQuery()
 * @method static \Illuminate\Database\Query\Builder|InReceiveFromDBP onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP query()
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereBeneficiaryAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereBeneficiaryAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereBeneficiaryAddress3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereBeneficiaryName1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereBeneficiaryName2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereBeneficiaryName3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereFundingCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereOutletName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereRemittanceDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereRemitterAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereRemitterAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereRemitterName1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereRemitterName2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereServiceCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereTransactionCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereTransactionRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveFromDBP whereUserUpdated($value)
 * @method static \Illuminate\Database\Query\Builder|InReceiveFromDBP withTrashed()
 * @method static \Illuminate\Database\Query\Builder|InReceiveFromDBP withoutTrashed()
 * @mixin \Eloquent
 */
class InReceiveFromDBP extends Model
{
    use HasFactory, UsesUuid, SoftDeletes;

    protected $table = 'in_receive_from_dbp';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
    */
    protected $fillable = [
        "user_account_id",
        "reference_number",
        "total_amount",
        "transaction_date",
        "transaction_category_id",
        "transaction_remarks",
        "file_name",
        "status",
        "user_created",
        "user_updated",
        "funding_currency",
        "remittance_date",
        "service_code",
        "total_amount",
        "outlet_name",
        "beneficiary_name_1",
        "beneficiary_name_2",
        "beneficiary_name_3",
        "beneficiary_address_1",
        "beneficiary_address_2",
        "beneficiary_address_3",
        "mobile_number",
        "message",
        "remitter_name_1",
        "remitter_name_2",
        "remitter_address_1",
        "remitter_address_2",
    ];

    public function sender() {
        return $this->hasOne(UserAccount::class, 'id', 'sender_id');
    }

    public function sender_details() {
        return $this->hasOne(UserDetail::class, 'user_account_id', 'sender_id');
    }

    public function user_account() {
        return $this->hasOne(UserAccount::class, 'id', 'user_account_id');
    }
}

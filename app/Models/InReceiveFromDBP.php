<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}

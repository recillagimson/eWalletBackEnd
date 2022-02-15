<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OutSend2Bank
 *
 * @property string $id
 * @property string $user_account_id
 * @property string $reference_number
 * @property string $account_name
 * @property string $account_number
 * @property string|null $send_receipt_to
 * @property string|null $remarks
 * @property string|null $particulars
 * @property string|null $message
 * @property string $amount
 * @property string $service_fee
 * @property string $service_fee_id
 * @property string $total_amount
 * @property \Illuminate\Support\Carbon $transaction_date
 * @property string $transaction_category_id
 * @property string $status
 * @property string $user_created
 * @property string|null $user_updated
 * @property string|null $deleted_at
 * @property string $provider
 * @property string|null $provider_transaction_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $provider_remittance_id
 * @property string|null $transaction_response
 * @property string $bank_code
 * @property string $bank_name
 * @property string $purpose
 * @property string|null $other_purpose
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank query()
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereAccountName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereBankCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereOtherPurpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereParticulars($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereProviderRemittanceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereProviderTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank wherePurpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereSendReceiptTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereServiceFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereServiceFeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereTransactionCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereTransactionResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSend2Bank whereUserUpdated($value)
 * @mixin \Eloquent
 */
class OutSend2Bank extends Model
{
    use UsesUuid, HasFactory;

    protected $table = 'out_send2banks';

    protected $fillable = [
        'user_account_id',
        'reference_number',
        'bank_code',
        'bank_name',
        'account_name',
        'account_number',
        'sender_recepient_to',
        'purpose',
        'other_purpose',
        'amount',
        'service_fee',
        'service_fee_id',
        'total_amount',
        'transaction_date',
        'transaction_category_id',
        'transaction_remarks',
        'status',
        'provider',
        'provider_reference',
        'send_receipt_to',
        'remarks',
        'particulars',
        'user_created',
        'user_updated',
    ];

    protected $casts = [
        'transaction_date' => 'datetime'
    ];
}

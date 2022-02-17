<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\KYCVerification
 *
 * @property string $id
 * @property string $user_account_id
 * @property string $request_id
 * @property string|null $application_id
 * @property string $transaction_id
 * @property string|null $hv_response
 * @property string|null $hv_result
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|KYCVerification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KYCVerification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KYCVerification query()
 * @method static \Illuminate\Database\Eloquent\Builder|KYCVerification whereApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KYCVerification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KYCVerification whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KYCVerification whereHvResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KYCVerification whereHvResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KYCVerification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KYCVerification whereRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KYCVerification whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KYCVerification whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KYCVerification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KYCVerification whereUserAccountId($value)
 * @mixin \Eloquent
 */
class KYCVerification extends Model
{
    use HasFactory, UsesUuid;

    protected $table = 'kyc_verifications';

    protected $fillable = [
        'user_account_id',
        'request_id',
        'application_id',
        'hv_response',
        'hv_result',
        'status',
        'transaction_id'
    ];
}

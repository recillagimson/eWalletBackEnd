<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\DRCRProcedure
 *
 * @property string|null $transaction_date
 * @property string|null $original_transaction_date
 * @property string|null $account_number
 * @property string $user_Account_id
 * @property string|null $last_name
 * @property string|null $first_name
 * @property string|null $middle_name
 * @property float $total_amount
 * @property string|null $reference_number
 * @property string $Status
 * @property string $Type
 * @property string|null $rsbsa_number
 * @property string|null $Description
 * @property string|null $provider_reference
 * @property string|null $transaction_category_id
 * @property string|null $category
 * @property string|null $category_description
 * @property-read mixed $transaction_date_manila_time
 * @property-read mixed $transaction_date_word_format_manila_time
 * @property-read \App\Models\UserDetail|null $user_details
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRProcedure newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRProcedure newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRProcedure query()
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRProcedure whereAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRProcedure whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRProcedure whereCategoryDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRProcedure whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRProcedure whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRProcedure whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRProcedure whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRProcedure whereOriginalTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRProcedure whereProviderReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRProcedure whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRProcedure whereRsbsaNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRProcedure whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRProcedure whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRProcedure whereTransactionCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRProcedure whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRProcedure whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRProcedure whereUserAccountId($value)
 * @mixin \Eloquent
 */
class DRCRProcedure extends Model
{
    use HasFactory;

    protected $table = 'running_balance';
    protected $appends = [
        'transaction_date_manila_time',
        'transaction_date_word_format_manila_time'
    ];

    public function getTransactionDateManilaTimeAttribute() {
        return Carbon::parse($this->transaction_date)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A');
    }

    public function getTransactionDateWordFormatManilaTimeAttribute() {
        return Carbon::parse($this->transaction_date)->setTimezone('Asia/Manila')->format('F d, Y h:i:s A');
    }

    public function user_details() {
        return $this->hasOne(UserDetail::class, 'id', 'user_Account_id');
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\BillerReport
 *
 * @property string|null $account_number
 * @property string|null $last_name
 * @property string|null $first_name
 * @property string|null $middle_name
 * @property string $reference_number
 * @property string|null $transaction_date
 * @property string $original_transaction_date
 * @property string $billers_name
 * @property string $total_amount
 * @property string $status
 * @property string $biller_reference_number
 * @property-read mixed $manila_time_transaction_date
 * @method static \Illuminate\Database\Eloquent\Builder|BillerReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillerReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillerReport query()
 * @method static \Illuminate\Database\Eloquent\Builder|BillerReport whereAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillerReport whereBillerReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillerReport whereBillersName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillerReport whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillerReport whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillerReport whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillerReport whereOriginalTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillerReport whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillerReport whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillerReport whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillerReport whereTransactionDate($value)
 * @mixin \Eloquent
 */
class BillerReport extends Model
{
    protected $table = 'billers_report';

    protected $appends = [
        'manila_time_transaction_date'
    ];

    public function getManilaTimeTransactionDateAttribute() {
        if($this->original_transaction_date) {
            return Carbon::parse($this->original_transaction_date)->setTimezone('Asia/Manila')->format('F d, Y h:i:s A');
        }
        return null;
    }
}

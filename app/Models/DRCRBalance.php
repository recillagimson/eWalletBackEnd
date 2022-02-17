<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\DRCRBalance
 *
 * @property-read mixed $manila_time_approved_at
 * @property-read mixed $manila_time_declined_at
 * @property-read mixed $manila_time_transaction_date
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRBalance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRBalance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRBalance query()
 * @mixin \Eloquent
 */
class DRCRBalance extends Model
{
    use HasFactory;

    protected $table = 'drcr_memo_with_running_balance_view';

    protected $appends = [
        'manila_time_transaction_date',
        'manila_time_approved_at',
        'manila_time_declined_at',
    ];

    public function getManilaTimeTransactionDateAttribute() {
        return Carbon::parse($this->transaction_date)->setTimezone('Asia/Manila')->format('F d, Y h:i A');
    }
    public function getManilaTimeApprovedAtAttribute() {
        return Carbon::parse($this->approved_at)->setTimezone('Asia/Manila')->format('F d, Y h:i A');
    }
    public function getManilaTimeDeclinedAtAttribute() {
        return Carbon::parse($this->declined_at)->setTimezone('Asia/Manila')->format('F d, Y h:i A');
    }
}

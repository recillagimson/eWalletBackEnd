<?php

namespace App\Models\DBP;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\DBP\DBPDisbursement
 *
 * @property-read mixed $manila_time_transaction_date
 * @method static \Illuminate\Database\Eloquent\Builder|DBPDisbursement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DBPDisbursement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DBPDisbursement query()
 * @mixin \Eloquent
 */
class DBPDisbursement extends Model
{
    use HasFactory;

    protected $table = 'dbp_disbursement_report_view';
    protected $appends = [
        'manila_time_transaction_date'
    ];

    public function getManilaTimeTransactionDateAttribute() {
        return Carbon::parse($this->original_transaction_date)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A');
    }
}

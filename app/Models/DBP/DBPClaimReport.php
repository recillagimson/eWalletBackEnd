<?php

namespace App\Models\DBP;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\DBP\DBPClaimReport
 *
 * @property-read mixed $manila_time_transaction_date
 * @method static \Illuminate\Database\Eloquent\Builder|DBPClaimReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DBPClaimReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DBPClaimReport query()
 * @mixin \Eloquent
 */
class DBPClaimReport extends Model
{
    use HasFactory;

    protected $table = 'dbp_claims_report';
    protected $appends = [
        'manila_time_transaction_date'
    ];

    public function getManilaTimeTransactionDateAttribute() {
        return Carbon::parse($this->original_transaction_date)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A');
    }
}

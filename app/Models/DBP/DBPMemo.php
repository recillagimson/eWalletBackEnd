<?php

namespace App\Models\DBP;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\DBP\DBPMemo
 *
 * @property-read mixed $manila_time_transaction_date
 * @method static \Illuminate\Database\Eloquent\Builder|DBPMemo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DBPMemo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DBPMemo query()
 * @mixin \Eloquent
 */
class DBPMemo extends Model
{
    use HasFactory;

    protected $table = 'dbp_drcr_memos_view';
    protected $appends = [
        'manila_time_transaction_date'
    ];

    public function getManilaTimeTransactionDateAttribute() {
        return Carbon::parse($this->original_transaction_date)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A');
    }
}

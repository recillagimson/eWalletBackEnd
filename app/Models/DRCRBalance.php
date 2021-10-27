<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        return Carbon::parse($this->transaction_date)->format('F d, Y h:i A');
    }
    public function getManilaTimeApprovedAtAttribute() {
        return Carbon::parse($this->approved_at)->format('F d, Y h:i A');
    }
    public function getManilaTimeDeclinedAtAttribute() {
        return Carbon::parse($this->declined_at)->format('F d, Y h:i A');
    }
}

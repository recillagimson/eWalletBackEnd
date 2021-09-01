<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BillerReport extends Model
{
    protected $table = 'billers_report';

    protected $appends = [
        'manila_time_transaction_date'
    ];

    public function getManilaTimeTransactionDateAttribute() {
        if($this->original_transaction_date) {
            return Carbon::parse($this->original_transaction_date)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A');
        }
        return null;
    }
}

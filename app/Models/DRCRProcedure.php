<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

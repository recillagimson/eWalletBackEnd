<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCountDailyView extends Model
{
    use HasFactory;

    protected $table = 'transaction_count_daily';
}

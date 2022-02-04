<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCountMonthlyView extends Model
{
    use HasFactory;

    protected $table = 'transaction_count_monthly';
}

<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCountWeeklyView extends Model
{
    use HasFactory;

    protected $table = 'transaction_count_weekly';
}

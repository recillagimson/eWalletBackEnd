<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Dashboard\TransactionCountWeeklyView
 *
 * @property int|null $count
 * @property int|null $week
 * @property int|null $year
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCountWeeklyView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCountWeeklyView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCountWeeklyView query()
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCountWeeklyView whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCountWeeklyView whereWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCountWeeklyView whereYear($value)
 * @mixin \Eloquent
 */
class TransactionCountWeeklyView extends Model
{
    use HasFactory;

    protected $table = 'transaction_count_weekly';
}

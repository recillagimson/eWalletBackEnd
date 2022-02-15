<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Dashboard\TransactionCountMonthlyView
 *
 * @property int|null $count
 * @property int|null $month
 * @property int|null $year
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCountMonthlyView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCountMonthlyView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCountMonthlyView query()
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCountMonthlyView whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCountMonthlyView whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCountMonthlyView whereYear($value)
 * @mixin \Eloquent
 */
class TransactionCountMonthlyView extends Model
{
    use HasFactory;

    protected $table = 'transaction_count_monthly';
}

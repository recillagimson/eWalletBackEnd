<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Dashboard\TransactionCountDailyView
 *
 * @property int|null $count
 * @property string|null $date
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCountDailyView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCountDailyView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCountDailyView query()
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCountDailyView whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCountDailyView whereDate($value)
 * @mixin \Eloquent
 */
class TransactionCountDailyView extends Model
{
    use HasFactory;

    protected $table = 'transaction_count_daily';
}

<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Dashboard\SignupCountMonthlyView
 *
 * @property int $count
 * @property int|null $month
 * @property int|null $year
 * @method static \Illuminate\Database\Eloquent\Builder|SignupCountMonthlyView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SignupCountMonthlyView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SignupCountMonthlyView query()
 * @method static \Illuminate\Database\Eloquent\Builder|SignupCountMonthlyView whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SignupCountMonthlyView whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SignupCountMonthlyView whereYear($value)
 * @mixin \Eloquent
 */
class SignupCountMonthlyView extends Model
{
    use HasFactory;

    protected $table = 'signup_count_monthly';
}

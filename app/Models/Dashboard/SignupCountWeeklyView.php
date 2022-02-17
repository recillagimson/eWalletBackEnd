<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Dashboard\SignupCountWeeklyView
 *
 * @property int $count
 * @property int|null $week
 * @property int|null $year
 * @method static \Illuminate\Database\Eloquent\Builder|SignupCountWeeklyView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SignupCountWeeklyView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SignupCountWeeklyView query()
 * @method static \Illuminate\Database\Eloquent\Builder|SignupCountWeeklyView whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SignupCountWeeklyView whereWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SignupCountWeeklyView whereYear($value)
 * @mixin \Eloquent
 */
class SignupCountWeeklyView extends Model
{
    use HasFactory;

    protected $table = 'signup_count_weekly';
}

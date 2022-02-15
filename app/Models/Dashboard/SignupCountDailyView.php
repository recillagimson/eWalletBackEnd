<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Dashboard\SignupCountDailyView
 *
 * @property int $count
 * @property string|null $date
 * @method static \Illuminate\Database\Eloquent\Builder|SignupCountDailyView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SignupCountDailyView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SignupCountDailyView query()
 * @method static \Illuminate\Database\Eloquent\Builder|SignupCountDailyView whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SignupCountDailyView whereDate($value)
 * @mixin \Eloquent
 */
class SignupCountDailyView extends Model
{
    use HasFactory;

    protected $table = 'signup_count_daily';
}

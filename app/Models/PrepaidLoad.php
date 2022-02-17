<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\PrepaidLoad
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PrepaidLoad newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PrepaidLoad newQuery()
 * @method static \Illuminate\Database\Query\Builder|PrepaidLoad onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PrepaidLoad query()
 * @method static \Illuminate\Database\Query\Builder|PrepaidLoad withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PrepaidLoad withoutTrashed()
 * @mixin \Eloquent
 */
class PrepaidLoad extends Model
{
    use UsesUuid, HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'prepaid_loads';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'old_prepaid_load_id',
        'prepaid_type',
        'reward_keyword',
        'amax_keyword',
        'amount',
        'status',
    ];
}

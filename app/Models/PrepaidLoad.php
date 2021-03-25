<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrepaidLoad extends Model
{
    use HasFactory;
    use SoftDeletes;

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

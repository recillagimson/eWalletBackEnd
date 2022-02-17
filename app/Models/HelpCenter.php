<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\HelpCenter
 *
 * @method static \Illuminate\Database\Eloquent\Builder|HelpCenter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HelpCenter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HelpCenter query()
 * @mixin \Eloquent
 */
class HelpCenter extends Model
{
    use UsesUuid, HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'help_centers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'image_location',
        'order',
        "user_created",
        "user_updated",
    ];
}

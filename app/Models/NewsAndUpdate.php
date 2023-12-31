<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\NewsAndUpdate
 *
 * @method static \Illuminate\Database\Eloquent\Builder|NewsAndUpdate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsAndUpdate newQuery()
 * @method static \Illuminate\Database\Query\Builder|NewsAndUpdate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsAndUpdate query()
 * @method static \Illuminate\Database\Query\Builder|NewsAndUpdate withTrashed()
 * @method static \Illuminate\Database\Query\Builder|NewsAndUpdate withoutTrashed()
 * @mixin \Eloquent
 */
class NewsAndUpdate extends Model
{
    use UsesUuid, HasFactory, SoftDeletes;

     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'news_and_updates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'image_location',
        'user_created',
        'user_updated',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CustomerView
 *
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerView query()
 * @mixin \Eloquent
 */
class CustomerView extends Model
{
    use HasFactory;

    protected $table = 'customer_list_view';
}

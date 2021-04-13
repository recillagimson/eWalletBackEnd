<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionCategory extends Model
{
    use UsesUuid, HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaction_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'old_transaction_category_id',
        'title',
        'name',
        'description',
        'status',
        'user_created',
<<<<<<< HEAD
        'user_updated',
    ];
=======
    ];

>>>>>>> master
}

<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionCategory extends Model
{
    use HasFactory, UsesUuid, SoftDeletes;

    protected $fillable = [
        'old_transaction_category_id',
        'name',
        'description',
        'status',
        'user_created',
        'user_updated',
    ];
}

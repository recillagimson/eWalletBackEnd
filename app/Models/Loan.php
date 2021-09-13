<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory, UsesUuid;

    protected $fillable = [
        'user_account_id',
        'reference_number',
        'status',
        'user_created',
        'user_updated'
    ];
}

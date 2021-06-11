<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccountNumber extends Model
{
    use UsesUuid, HasFactory;

    protected $fillable = [
        'account_date',
        'counter',
    ];

    protected $casts = [
        'account_date' => 'datetime',
    ];
}

<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderBank extends Model
{
    use UsesUuid, HasFactory;

    protected $fillable = [
        'provider',
        'code',
        'name'
    ];
}

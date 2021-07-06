<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class ForeignExchangeRate extends Model
{
    use HasFactory, SoftDeletes, UsesUuid;

    protected $fillable = [
        'name',
        'code',
        'rate'
    ];


}

<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FarmerSubsidy extends Model
{
    use HasFactory, UsesUuid;

    protected $table = 'farmer_subsidies';

    protected $fillable = [
        'filename',
        'province',
        'seq',
        'filename',
        'success',
        'fails',
    ];
}

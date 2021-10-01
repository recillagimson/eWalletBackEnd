<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreferredCashOutPartner extends Model
{
    use HasFactory, UsesUuid;

    protected $fillable = [
        "description",
        "status",
        "user_created",
        "user_updated",
    ];
}

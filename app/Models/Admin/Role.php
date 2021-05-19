<?php

namespace App\Models\Admin;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes, UsesUuid;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'user_created',
        'user_updated'
    ];
}

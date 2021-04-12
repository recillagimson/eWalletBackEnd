<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'id_types';

    protected $fillable =[
        'id',
        'type',
        'description',
        'swirecommended',
        'status',
        'user_created',
        'user_updated'
    ];
}

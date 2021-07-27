<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdType extends Model
{
    use HasFactory, SoftDeletes;
    use UsesUuid;

    protected $table = 'id_types';

    protected $fillable =[
        'type',
        'description',
        'swirecommended',
        'is_ekyc',
        'is_full_name',
        'status',
        'user_created',
        'user_updated'
    ];
}

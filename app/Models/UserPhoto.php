<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPhoto extends Model
{
    use HasFactory, SoftDeletes, UsesUuid;

    protected $table = 'user_id_photos';

    protected $fillable = [
        'id',
        'user_account_id',
        'id_type_id',
        'old_id_type',
        'photo_location',
        'approval_status',
        'reviewed_by',
        'user_created',
        'user_updated'
    ];
}

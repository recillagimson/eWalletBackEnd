<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;
    use UsesUuid;

    protected $table = 'notifications';

    protected $fillable = [
        'id', 'title', 'status', 'description', 'user_account_id', 'user_created', 'user_updated',
    ];
}

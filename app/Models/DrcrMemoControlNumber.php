<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class DrcrMemoControlNumber extends Model
{
    use HasFactory, UsesUuid;
    
    protected $table = 'drcr_memo_control_numbers';

    protected $fillable = [
        'control_number',
        'num_rows',
        'status',
        'user_created',
        'user_updated'
    ];

}

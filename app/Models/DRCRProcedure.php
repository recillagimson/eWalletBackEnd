<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DRCRProcedure extends Model
{
    use HasFactory;

    protected $table = 'running_balance';

    public function user_details() {
        return $this->hasOne(UserDetail::class, 'id', 'user_Account_id');
    }
}

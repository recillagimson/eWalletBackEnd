<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerListView extends Model
{
    use HasFactory;

    protected $table = 'user_module_customer_list_view';

    protected $appends = [
        'manila_time_created_at',
        'manila_time_verified_at',
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function getManilaTimeCreatedAtAttribute() {
        return Carbon::parse($this->original_created_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A');
    }

    public function getManilaTimeVerifiedAtAttribute() {
        return Carbon::parse($this->original_verified_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A');
    }
}

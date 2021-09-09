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
        if($this && $this->original_created_at) {
            return Carbon::parse($this->original_created_at)->setTimezone('Asia/Manila')->format('F d, Y h:i:s A');
        }
        return null;
    }

    public function getManilaTimeVerifiedAtAttribute() {
        // dd($this->original_verified_date);
        if($this && $this->original_verified_date) {
            return Carbon::parse($this->original_verified_date)->setTimezone('Asia/Manila')->format('F d, Y h:i:s A');
        }
        return null;
    }
}

<?php

namespace App\Models\DBP;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DBPOnBoarding extends Model
{
    use HasFactory;

    protected $table = 'dbp_on_boarding_list_view';
    protected $appends = [
        'manila_time_created_at',
        'manila_time_approved_at'
    ];

    public function getManilaTimeCreatedAtAttribute() {
        return Carbon::parse($this->original_created_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A');
    }
    public function getManilaTimeApprovedAtAttribute() {
        return Carbon::parse($this->original_created_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A');
    }
}

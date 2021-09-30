<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LogHistory extends Model
{
    use HasFactory, SoftDeletes;
    use UsesUuid;

    protected $table ='log_histories';

    protected $appends = [
        'manila_time_created_at',
    ];

    protected $fillable = [
        "user_account_id",
        "reference_number",
        "squidpay_module",
        "namespace",
        "transaction_date",
        "remarks",
        "operation",
        "user_created",
        "user_updated",
    ];

    public function getManilaTimeCreatedAtAttribute() {
        if($this && $this->created_at) {
            return Carbon::parse($this->created_at)->setTimezone('Asia/Manila')->format('F d, Y h:i:s A');
        }
        return null;
    }
}

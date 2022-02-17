<?php

namespace App\Models\DBP;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\DBP\DBPCustomerList
 *
 * @property-read mixed $manila_time_created_at
 * @method static \Illuminate\Database\Eloquent\Builder|DBPCustomerList newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DBPCustomerList newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DBPCustomerList query()
 * @mixin \Eloquent
 */
class DBPCustomerList extends Model
{
    use HasFactory;

    protected $table = 'dbp_customer_list';
    protected $appends = [
        'manila_time_created_at'
    ];

    public function getManilaTimeCreatedAtAttribute() {
        return Carbon::parse($this->created_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A');
    }
}

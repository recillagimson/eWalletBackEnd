<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\CustomerListView
 *
 * @property string $id
 * @property string|null $account_number
 * @property string|null $email
 * @property string|null $first_name
 * @property string|null $middle_name
 * @property string|null $last_name
 * @property string|null $mobile_number
 * @property int $is_active
 * @property string|null $account_status
 * @property string|null $tier_class
 * @property string|null $original_created_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property string|null $original_verified_date
 * @property string|null $verified_date
 * @property string|null $name
 * @property string|null $tier_approval_id
 * @property-read mixed $manila_time_created_at
 * @property-read mixed $manila_time_verified_at
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerListView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerListView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerListView query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerListView whereAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerListView whereAccountStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerListView whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerListView whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerListView whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerListView whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerListView whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerListView whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerListView whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerListView whereMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerListView whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerListView whereOriginalCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerListView whereOriginalVerifiedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerListView whereTierApprovalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerListView whereTierClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerListView whereVerifiedDate($value)
 * @mixin \Eloquent
 */
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

<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\LogHistory
 *
 * @property string $id
 * @property string $user_account_id
 * @property string|null $reference_number
 * @property string|null $squidpay_module
 * @property string|null $namespace
 * @property string $transaction_date
 * @property string $remarks
 * @property string|null $operation
 * @property string $user_created
 * @property string $user_updated
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $manila_time_created_at
 * @method static \Illuminate\Database\Eloquent\Builder|LogHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LogHistory newQuery()
 * @method static \Illuminate\Database\Query\Builder|LogHistory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LogHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|LogHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogHistory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogHistory whereNamespace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogHistory whereOperation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogHistory whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogHistory whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogHistory whereSquidpayModule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogHistory whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogHistory whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogHistory whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogHistory whereUserUpdated($value)
 * @method static \Illuminate\Database\Query\Builder|LogHistory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|LogHistory withoutTrashed()
 * @mixin \Eloquent
 */
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

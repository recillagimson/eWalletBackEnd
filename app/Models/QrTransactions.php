<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\QrTransactions
 *
 * @property string $id
 * @property string $user_account_id
 * @property string $amount
 * @property int|null $status
 * @property string|null $user_created
 * @property string|null $user_updated
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $message
 * @method static \Illuminate\Database\Eloquent\Builder|QrTransactions newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QrTransactions newQuery()
 * @method static \Illuminate\Database\Query\Builder|QrTransactions onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|QrTransactions query()
 * @method static \Illuminate\Database\Eloquent\Builder|QrTransactions whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QrTransactions whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QrTransactions whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QrTransactions whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QrTransactions whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QrTransactions whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QrTransactions whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QrTransactions whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QrTransactions whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QrTransactions whereUserUpdated($value)
 * @method static \Illuminate\Database\Query\Builder|QrTransactions withTrashed()
 * @method static \Illuminate\Database\Query\Builder|QrTransactions withoutTrashed()
 * @mixin \Eloquent
 */
class QrTransactions extends Model
{
    use HasFactory, UsesUuid, SoftDeletes;

    protected $fillable = [
        'user_account_id',
        'amount',
        'status',
        'user_updated',
        'user_created',
        'message',
    ];

}

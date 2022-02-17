<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\TransactionCategory
 *
 * @property string $id
 * @property string $old_transaction_category_id
 * @property string $title
 * @property string $name
 * @property string $description
 * @property int $status
 * @property string $transactable
 * @property string $transaction_type
 * @property string $user_created
 * @property string|null $user_updated
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCategory newQuery()
 * @method static \Illuminate\Database\Query\Builder|TransactionCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCategory whereOldTransactionCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCategory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCategory whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCategory whereTransactable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCategory whereTransactionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCategory whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransactionCategory whereUserUpdated($value)
 * @method static \Illuminate\Database\Query\Builder|TransactionCategory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TransactionCategory withoutTrashed()
 * @mixin \Eloquent
 */
class TransactionCategory extends Model
{
    use UsesUuid, HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaction_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'old_transaction_category_id',
        'title',
        'name',
        'description',
        'status',
        'user_created',
        'user_updated',
    ];
}

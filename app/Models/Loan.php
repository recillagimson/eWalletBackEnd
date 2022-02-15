<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Loan
 *
 * @property string $id
 * @property string $user_account_id
 * @property string $reference_number
 * @property string $status
 * @property string $user_created
 * @property string $user_updated
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Loan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Loan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Loan query()
 * @method static \Illuminate\Database\Eloquent\Builder|Loan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Loan whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Loan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Loan whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Loan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Loan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Loan whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Loan whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Loan whereUserUpdated($value)
 * @mixin \Eloquent
 */
class Loan extends Model
{
    use HasFactory, UsesUuid;

    protected $fillable = [
        'user_account_id',
        'reference_number',
        'status',
        'user_created',
        'user_updated'
    ];
}

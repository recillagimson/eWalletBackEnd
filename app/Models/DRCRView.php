<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DRCRView
 *
 * @property string $ID
 * @property string|null $account_number
 * @property string $user_account_id
 * @property string|null $last_name
 * @property string|null $first_name
 * @property string|null $middle_name
 * @property string $type_of_memo
 * @property string $reference_number
 * @property string $amount
 * @property string|null $category
 * @property string|null $description
 * @property string|null $remarks
 * @property string $status
 * @property string|null $user_created
 * @property string|null $user_created_name
 * @property string|null $approved_by
 * @property string|null $approved_by_name
 * @property string|null $declined_by
 * @property string|null $declined_by_name
 * @property string|null $transaction_date
 * @property string|null $transaction_date_for_fe
 * @property string|null $approved_at
 * @property string|null $approved_at_for_fe
 * @property string|null $declined_at
 * @property string|null $declined_at_for_fe
 * @property string|null $available_balance
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView query()
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView whereAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView whereApprovedAtForFe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView whereApprovedByName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView whereAvailableBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView whereDeclinedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView whereDeclinedAtForFe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView whereDeclinedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView whereDeclinedByName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView whereID($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView whereTransactionDateForFe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView whereTypeOfMemo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DRCRView whereUserCreatedName($value)
 * @mixin \Eloquent
 */
class DRCRView extends Model
{
    use HasFactory;

    protected $table = 'drcr_memos_view';
}

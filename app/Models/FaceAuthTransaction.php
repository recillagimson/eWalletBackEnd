<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FaceAuthTransaction
 *
 * @property string $id
 * @property string $transaction_id
 * @property string $rsbsa_number
 * @property string $user_account_id
 * @property string|null $response
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|FaceAuthTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FaceAuthTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FaceAuthTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|FaceAuthTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FaceAuthTransaction whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FaceAuthTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FaceAuthTransaction whereResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FaceAuthTransaction whereRsbsaNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FaceAuthTransaction whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FaceAuthTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FaceAuthTransaction whereUserAccountId($value)
 * @mixin \Eloquent
 */
class FaceAuthTransaction extends Model
{
    use HasFactory, UsesUuid;

    protected $table = 'face_auth_transactions';

    protected $fillable = [
        "transaction_id",
        "rsbsa_number",
        "user_account_id",
        "response",
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

/**
 * App\Models\DrcrMemoControlNumber
 *
 * @property string $id
 * @property string $control_number
 * @property int|null $num_rows
 * @property string|null $status
 * @property string $user_created
 * @property string $user_updated
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemoControlNumber newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemoControlNumber newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemoControlNumber query()
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemoControlNumber whereControlNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemoControlNumber whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemoControlNumber whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemoControlNumber whereNumRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemoControlNumber whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemoControlNumber whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemoControlNumber whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemoControlNumber whereUserUpdated($value)
 * @mixin \Eloquent
 */
class DrcrMemoControlNumber extends Model
{
    use HasFactory, UsesUuid;

    protected $table = 'drcr_memo_control_numbers';

    protected $fillable = [
        'control_number',
        'num_rows',
        'status',
        'user_created',
        'user_updated'
    ];
}

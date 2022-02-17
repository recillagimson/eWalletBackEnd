<?php

namespace App\Models\Utilities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Utilities\Otp
 *
 * @property int $id
 * @property string $identifier
 * @property string $token
 * @property int $validity
 * @property int $expired
 * @property int $no_times_generated
 * @property int $no_times_attempted
 * @property \Illuminate\Support\Carbon $generated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $validated
 * @method static \Illuminate\Database\Eloquent\Builder|Otp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Otp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Otp query()
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereExpired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereGeneratedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereNoTimesAttempted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereNoTimesGenerated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereValidated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereValidity($value)
 * @mixin \Eloquent
 */
class Otp extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'identifier', 'token', 'validity','expired','no_times_generated','generated_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    public function isExpired() :bool
    {
        if ($this->expired) {
            return true;
        }

        $generatedTime = $this->generated_at->addMinutes($this->validity);
        $currentTime = Carbon::now();

        if ($currentTime->lessThanOrEqualTo($generatedTime)) {
            return false;
        }
        $this->expired = true;
        $this->save();

        return true;
    }

    public function expiredAt() :object
    {
        return $this->generated_at->addMinutes($this->validity);
    }
}

<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Payload
 *
 * @property string $id
 * @property string $payloadType
 * @property string $passPhrase
 * @method static \Illuminate\Database\Eloquent\Builder|Payload newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payload newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payload query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payload whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payload wherePassPhrase($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payload wherePayloadType($value)
 * @mixin \Eloquent
 */
class Payload extends Model
{
    use UsesUuid;

    public $timestamps = false;

    protected $fillable = ['payloadType', 'passPhrase'];
}

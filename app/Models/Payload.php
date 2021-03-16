<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Payload extends Model
{
    use UsesUuid;

    public $timestamps = false;

    protected $fillable = ['payloadType', 'passPhrase'];
}

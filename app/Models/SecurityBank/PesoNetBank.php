<?php

namespace App\Models\SecurityBank;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PesoNetBank extends Model
{
    use HasFactory, SoftDeletes, UsesUuid;

    protected $table = 'sec_pesonet_banks';

    protected $fillable = [
        'bank_name',
        'bank_bic'
    ];
}

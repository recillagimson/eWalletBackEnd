<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class FarmerImport extends Model
{
    use HasFactory, UsesUuid;

    protected $table = "farmer_imports";

    protected $fillable = [
        "filename",
        "seq",
        "province",
        "success",
        "fails",
    ];
}

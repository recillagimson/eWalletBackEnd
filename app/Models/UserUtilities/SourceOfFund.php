<?php

namespace App\Models\UserUtilities;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SourceOfFund extends Model
{
    use UsesUuid, HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "source_of_funds";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "description",
        "status",
        "user_created",
        "user_updated"
    ];
}

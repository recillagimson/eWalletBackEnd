<?php

namespace App\Exceptions;

use Exception;

class TierLimitException extends Exception
{
    public function render()
    {
        return response()->json(['message' => 'Requested amount exceeded the user`s limits'], 400);
    }
}

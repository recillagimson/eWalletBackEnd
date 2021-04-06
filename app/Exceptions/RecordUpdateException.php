<?php

namespace App\Exceptions;

use Exception;

class RecordUpdateException extends Exception
{
    public function render()
    {
        return response()->json(['message' => 'Record is already updated.'], 400);
    }
}

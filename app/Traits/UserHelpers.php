<?php


namespace App\Traits;


use App\Enums\UsernameTypes;
use Illuminate\Http\Request;

trait UserHelpers
{
    private function getUsernameField(Request $request): string
    {
        return $request->has(UsernameTypes::Email) ? UsernameTypes::Email : UsernameTypes::MobileNumber;
    }
}

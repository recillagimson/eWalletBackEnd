<?php


namespace App\Traits;


use App\Enums\OtpTypes;
use App\Enums\UserKeyTypes;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

trait RouteParamHelpers
{
    private function getOtpTypeFromUserKeyType(string $keyType): string
    {
        return $keyType === UserKeyTypes::pin ? OtpTypes::pinRecovery : OtpTypes::passwordRecovery;
    }

    private function validateUserKeyTypes(string $keyType)
    {
        if (!in_array($keyType, UserKeyTypes::values))
            throw new RouteNotFoundException();
    }


}

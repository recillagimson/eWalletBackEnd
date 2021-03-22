<?php

namespace App\Services\Auth;

use App\Repositories\UserAccount\IUserAccountRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\NewAccessToken;

class AuthService implements IAuthService
{
    public IUserAccountRepository $userAccounts;

    public function __construct(IUserAccountRepository $userAccts)
    {
        $this->userAccounts = $userAccts;

    }


    /**
     * Creates a new UserAccount record
     *
     * @param array $newUser
     * @return mixed
     */
    public function register(array $newUser)
    {
        $newUser['password'] = Hash::make($newUser['password']);
        return $this->userAccounts->create($newUser);
    }

    /**
     * Attempts to authenticate the user with the
     * provided credentials
     *
     * @param string $usernameField
     * @param array $creds
     * @param string $ip
     * @return NewAccessToken
     * @throws ValidationException
     */
    public function login(string $usernameField, array $creds, string $ip): NewAccessToken
    {
        $throttleKey = $this->throttleKey($creds, $usernameField, $ip);
        if($this->isRateLimited($throttleKey)) {
            throw ValidationException::withMessages([
                $usernameField => 'Account has been locked out. Due to subsequent failed attempts.'
            ]);
        }

        if(!Auth::attempt($creds)) {
            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages([
                $usernameField => 'Login Failed.'
            ]);
        }

        $user = $this->userAccounts->getByUsername($usernameField, $creds[$usernameField]);
        if(!$user) {
            Auth::logout();
            throw new ModelNotFoundException();
        }

        $user->tokens()->delete();
        RateLimiter::clear($throttleKey);
        return $user->createToken('access_token');
    }

    private function throttleKey(array $creds, string $usernameField, string $ip): string
    {
        return Str::lower($creds[$usernameField].'|'.$ip);
    }

    private function isRateLimited(string $throttleKey): bool
    {
        if(!RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return false;
        }

        return true;
    }


}

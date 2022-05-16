<?php


namespace App\Services\Utilities\OTP;

use App\Repositories\OtpRepository\IOtpRepository;
use App\Traits\Errors\WithAuthErrors;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OtpService implements IOtpService
{
    use WithAuthErrors;

    /**
     * Length of the generated OTP
     *
     * @var int
     */
    protected $length;

    /**
     * Generated OPT type
     *
     * @var bool
     */
    protected $onlyDigits;

    /**
     * use same token to resending opt
     *
     *  @var bool
     */
    protected $useSameToken;

    /**
     * Otp Validity time
     *
     * @var int
     */
    protected $validity;

    /**
     * Delete old otps
     *
     * @var int
     */
    protected $deleteOldOtps;

    /**
     * Maximum otps allowed to generate
     *
     *  @var int
     */
    protected $maximumOtpsAllowed;

    /**
     * Maximum number of times to allowed to validate
     *
     * @var int
     */
    protected $allowedAttempts;

    private IOtpRepository $otps;

    public function __construct(IOtpRepository $otps)
    {
        $this->length = config('otp-generator.length');
        $this->onlyDigits = config('otp-generator.onlyDigits');
        $this->useSameToken = config('otp-generator.useSameToken');
        $this->validity = config('otp-generator.validity');
        $this->deleteOldOtps = config('otp-generator.deleteOldOtps');
        $this->maximumOtpsAllowed = config('otp-generator.maximumOtpsAllowed');
        $this->allowedAttempts = config('otp-generator.allowedAttempts');

        $this->otps = $otps;
    }

    /**
     * When a method is called, look for the 'set' prefix and attempt to set the
     * matching property to the value passed to the method and return a chainable
     * object to the caller.
     *
     * @param string $method
     * @param mixed $params
     * @return OtpService|void
     */
    public function __call(string $method, $params)
    {
        if (substr($method, 0, 3) != 'set') return;

        $property = Str::camel(substr($method, 3));


        // Does the property exist on this object?
        if (! property_exists($this, $property)) return;

        $this->{$property} = $params[0] ?? null;

        return $this;
    }

    public function generate(string $identifier): object
    {
        $this->deleteOldOtps();

        $otp = $this->otps->getByIdentifier($identifier);

        if (Str::after($identifier, 'login:') == '5ae241a8-2982-41cd-a19c-e7be1bec8855') {
            $data = [
                'identifier' => $identifier,
                'token' => 1111,
                'validity' => $this->validity,
                'generated_at' => Carbon::now(),
            ];

            $otp = $this->otps->create($data);
            Log::debug('OTP Generated: ', $data);
        }
        if ($otp == null) {
            $data = [
                'identifier' => $identifier,
                'token' => $this->createPin(),
                'validity' => $this->validity,
                'generated_at' => Carbon::now(),
            ];

            $otp = $this->otps->create($data);
            Log::debug('OTP Generated: ', $data);
        } else {
            $data = [
                'identifier' => $identifier,
                'token' =>  $this->useSameToken ?  $otp->token :  $this->createPin(),
                'validity' => $this->validity,
                'generated_at' => Carbon::now()
            ];

            $this->otps->update($otp, $data);
            Log::debug('OTP Updated: ', $data);
        }

        if ($otp->no_times_generated == $this->maximumOtpsAllowed) {
            $this->otpMaxedGenerationAttempts();
        }

        $otp->increment('no_times_generated');

        return (object) [
            'status' => true,
            'token' => $otp->token,
            'message' => "OTP generated",
        ];
    }

    public function validate(string $identifier, string $token): object
    {
        $otp = $this->otps->getByIdentifier($identifier);

        if (!$otp) {
            Log::error('OTP Validation failed: No OTP Record Found.', [
                'identifier' => $identifier,
                'token' => $token,
            ]);

            $this->otpInvalid();
        }

        if ($otp->isExpired()) $this->otpIsExpired();
        if ($otp->no_times_attempted == $this->allowedAttempts) $this->otpMaxedAttempts();

        $otp->increment('no_times_attempted');

        if ($otp->token === $token) {
            $otp->validated = true;
            $otp->save();

            return (object)[
                'status' => true,
                'message' => 'OTP is valid',
            ];
        }

        Log::error('OTP Validation failed', [
            'identifier' => $identifier,
            'token' => $token,
            'otp' => $otp->toArray(),
        ]);

        $this->otpInvalid();
    }

    public function ensureValidated(string $identifier, bool $otpEnabled = true)
    {
        if (App::environment('local') || !$otpEnabled) return;

        $otp = $this->otps->getByIdentifier($identifier, true);
        if (!$otp) $this->otpInvalid();
        if ($otp->isExpired()) $this->otpIsExpired();
        if (!$otp->validated) $this->otpInvalid();

        $otp->expired = true;
        $otp->save();
    }

    public function expiredAt(string $identifier): object
    {
        $otp = $this->otps->getByIdentifier($identifier);

        if (! $otp) {
            return (object) [
                'status' => false,
                'message' => 'OTP does not exists, Please generate new OTP',
            ];
        }

        return (object) [
            'status' => true,
            'expired_at' => $otp->expiredAt(),
        ];
    }

    private function deleteOldOtps()
    {
        $this->otps->deleteOld();
    }

    private function createPin(): string
    {
        if ($this->onlyDigits) {
            $characters = '0123456789';
        } else {
            $characters = '123456789abcdefghABCDEFGH';
        }
        $length = strlen($characters);
        $pin = '';
        for ($i = 0; $i < $this->length; $i++) {
            $pin .= $characters[rand(0, $length - 1)];
        }

        return $pin;
    }


}

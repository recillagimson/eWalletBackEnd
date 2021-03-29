<?php


namespace App\Services\Utilities\Notifications;


use App\Services\Utilities\API\IApiService;
use Illuminate\Validation\ValidationException;

class SmsService implements INotificationService
{
    protected string $apiUrl;

    protected string $username;

    protected string $password;

    protected string $shotcodeMask;

    private IApiService $apiService;

    public function __construct(IApiService $apiService)
    {
        $this->apiUrl = config('sms.api_url');
        $this->username = config('sms.username');
        $this->password = config('sms.password');
        $this->shotcodeMask = config('sms.shortcode_mask');

        $this->apiService = $apiService;
    }

    public function sendPasswordVerification(string $to, string $otp)
    {
        $url = $this->apiUrl.'/broadcast';
        $content = 'Your password recovery code is: '.$otp;
        $message = $this->getMessage($to, $content);

        $response = $this->apiService->post($url, $message);
        if(!$response->successful()) $this->sendingFailed();
    }

    private function getMessage(string $to, string $content): array
    {
        return [
            'username' => $this->username,
            'password' => $this->password,
            'msisdn' => $to,
            'content' => $content,
            'shortcode_mask' => $this->shotcodeMask,
        ];
    }

    private function sendingFailed()
    {
        throw ValidationException::withMessages([
           'sms' => 'SMS provider failed to send the message. Please try again.'
        ]);
    }
}

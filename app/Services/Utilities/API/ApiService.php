<?php


namespace App\Services\Utilities\API;


use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ApiService implements IApiService
{
    private array $defaultHeaders = [
        'Accept' => 'application/json',
    ];

    public function post(string $url, array $data): Response
    {
        return Http::post($url, $data);
    }

    public function postAsForm(string $url, array $data, array $headers = null): Response
    {
        if(!$headers) {
            $headers = $this->defaultHeaders;
        }

        return Http::withHeaders($headers)->asForm()->post($url, $data);
    }
}

<?php


namespace App\Services\Utilities\API;


use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ApiService implements IApiService
{
    private array $defaultHeaders = [
        'Accept' => 'application/json',
    ];

    public function get(string $url, array $headers = null): Response
    {
        if (!$headers) {
            $headers = $this->defaultHeaders;
        }

        return Http::withHeaders($headers)->withOptions(['verify' => false])->get($url);
    }

    public function post(string $url, array $data, array $headers = null): Response
    {
        if (!$headers) {
            $headers = $this->defaultHeaders;
        }

        return Http::withHeaders($headers)->withOptions(['verify' => false])->post($url, $data);
    }

    public function postXml(string $url, string $xml, array $headers = null): Response
    {
        if (!$headers) {
            $headers = $this->defaultHeaders;
        }

        return Http::withHeaders($headers)->send('POST', $url, [
            'body' => $xml
        ]);
    }

    public function postAsForm(string $url, array $data, array $headers = null): Response
    {
        if (!$headers) {
            $headers = $this->defaultHeaders;
        }

        return Http::withHeaders($headers)->withOptions(['verify' => false])->asForm()->post($url, $data);
    }


}

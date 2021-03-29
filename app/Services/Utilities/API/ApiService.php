<?php


namespace App\Services\Utilities\API;


use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ApiService implements IApiService
{

    public function post(string $url, array $data): Response
    {
        return Http::post($url, $data);
    }
}

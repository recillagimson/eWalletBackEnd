<?php


namespace App\Services\Utilities\API;


use Illuminate\Http\Client\Response;

interface IApiService
{
    public function post(string $url, array $data): Response;
}

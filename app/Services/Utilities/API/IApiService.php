<?php


namespace App\Services\Utilities\API;


use Illuminate\Http\Client\Response;

interface IApiService
{
    public function post(string $url, array $data): Response;
    public function postAsForm(string $url, array $data, array $headers = null): Response;
}

<?php


namespace App\Services\Utilities\API;


use Illuminate\Http\Client\Response;

interface IApiService
{
    public function get(string $url, array $headers = null): Response;

    public function post(string $url, array $data, array $headers = null): Response;

    public function postXml(string $url, string $xml, array $headers = null): Response;

    public function postAsForm(string $url, array $data, array $headers = null): Response;
}

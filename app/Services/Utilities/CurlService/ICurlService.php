<?php


namespace App\Services\Utilities\CurlService;


use Illuminate\Http\Client\Response;

interface ICurlService
{
public function curlPost($url, $data, $headers = []);
}

<?php


namespace App\Services\Utilities\CurlService;


use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class CurlService implements ICurlService
{
    public function curlPost($url, $data, $headers = [])  {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $headers
        ));


        $response = curl_exec($curl);

        curl_close($curl);
        return (Array) json_decode($response);
    }
}

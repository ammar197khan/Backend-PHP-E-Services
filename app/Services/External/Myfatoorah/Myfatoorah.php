<?php

namespace App\Services\External\Myfatoorah;

class Myfatoorah
{
    public static function call($path, $payload, $version = 'v2/')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, config('myfatoorah.server').'/'.$version.$path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Accept: application/json';
        $headers[] = 'Authorization: bearer ' . config('myfatoorah.api_key');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return json_decode($result, true);
    }
}

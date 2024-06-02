<?php

namespace App\Services\External;

class SMS
{
    public static function send($phone, $message)
    {
//        if(substr($phone, 0, 3) != 966){
//            $phone = '966' . $phone;
//        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://www.msegat.com/gw/sendsms.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);

        curl_setopt($ch, CURLOPT_POST, TRUE);

        $fields = [
          "userName"   => "AhmedQreeb",
          "numbers"    => "$phone",
          "userSender" => "Qreeb",
          "apiKey"     => "9240cb77846d5fd794d3692132e9d064",
          "msg"        => "$message"
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        return $response;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushNotify extends Model
{
    public static function user_send($tokens,$ar_text,$en_text,$type,$order_id=null,$extra=null, $order_cost = 0, $lang = 'en')
    {
        $fields = array
        (
            "registration_ids" => $tokens,
            "priority" => 10,
            'data' => [
                'type'       => $type,
                'ar_text'    => $ar_text,
                'en_text'    => $en_text,
                'order_id'   => $order_id,
                'extra'      => $extra,
                'sound'      => 'default',
                'order_cost' => $order_cost,
            ],
            'notification' => [
                'type'       => $type,
                'body'       => $lang == 'en' ? $en_text : $ar_text,
                'en_text'    => $en_text,
                'order_id'   => $order_id,
                'extra'      => $extra,
                'title'      => "",
                'sound'      => 'default',
                'order_cost' => $order_cost,
            ],
            'vibrate' => 1,
            'sound' => 1
        );
        $headers = array
        (
            'accept: application/json',
            'Content-Type: application/json',
            'Authorization: key=' .
            'AAAAsIT-oDw:APA91bGgD_4Mb1vcy52B-0gbOpSwIIOFPE15aOzZSxnSbPWLdWsexSvKiFWRrcfgIvc7mGe5Iz7uusZR_tsrnL7apevTv-EdOvkeI4SJzDium4TQzj0ZjIZPlZ-dnuFETwxLD28Rlb0a'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        //  var_dump($result);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);

        return $result;
    }


    public static function tech_send($tokens,$ar_text,$en_text,$type,$order_id=null,$extra=null, $order_cost = 0, $lang = 'en')
    {
        $fields = array
        (
            "registration_ids" => $tokens,
            "priority" => 10,
            'data' => [
                'type'       => $type,
                'ar_text'    => $ar_text,
                'en_text'    => $en_text,
                'order_id'   => $order_id,
                'extra'      => $extra,
                'sound'      => 'default',
                'order_cost' => $order_cost,
            ],
            'notification' => [
                'type'       => $type,
                'body'       =>  $lang == 'en' ? $en_text : $ar_text,
                'en_text'    => $en_text,
                'order_id'   => $order_id,
                'extra'      => $extra,
                'title'      => "",
                'sound'      => 'default',
                'order_cost' => $order_cost,
            ],
            'vibrate' => 1,
            'sound' => 1
        );
        $headers = array
        (
            'accept: application/json',
            'Content-Type: application/json',
            'Authorization: key=' .
            'AAAAsIT-oDw:APA91bGgD_4Mb1vcy52B-0gbOpSwIIOFPE15aOzZSxnSbPWLdWsexSvKiFWRrcfgIvc7mGe5Iz7uusZR_tsrnL7apevTv-EdOvkeI4SJzDium4TQzj0ZjIZPlZ-dnuFETwxLD28Rlb0a'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        //  var_dump($result);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);

        return $result;
    }

    public static function sendSms($activation_code,$phone)
    {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://www.msegat.com/gw/sendsms.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);

        curl_setopt($ch, CURLOPT_POST, TRUE);

        $message = "كود التفعيل الخاص بشركة قريب هو".$activation_code;
        $message = urlencode($message);

        $fields = <<<EOT
        {
          "userName": "AhmedQreeb",
          "numbers": "$phone",
          "userSender": "Qreeb",
          "apiKey": "9240cb77846d5fd794d3692132e9d064",
          "msg": "$message"
        }
EOT;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",));

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
    }

    public static function payment($Key,$KeyType)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://apitest.myfatoorah.com/v2/GetPaymentStatus");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);

        curl_setopt($ch, CURLOPT_POST, TRUE);

        $fields = <<<EOT
        {
          "Key": $Key,
          "KeyType": $KeyType,
        }
EOT;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",));

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
    }
}

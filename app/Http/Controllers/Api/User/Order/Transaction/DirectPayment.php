<?php

namespace App\Http\Controllers\Api\User\Order\Transaction;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DirectPayment extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'            => 'required|exists:users,id',
            'jwt'                => 'required|exists:users,jwt,id,'.$request->user_id,
            'order_id'           => 'required|exists:orders,id,user_id,'.$request->user_id,

            'invoice_key'        => 'required',
            'payment_gateway_id' => 'required',

            'payment_type'       => 'required',
            'save_token'         => 'required|boolean',
            'is_recurring'       => 'required|boolean',
            'interval_days'      => 'required|integer',
            'token'              => 'required',
            'bypass_3DS'         => 'required|boolean',
            'card_number'        => 'required',
            'card_expiry_month'  => 'required',
            'card_expiry_year'   => 'required',
            'card_security_code' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $invoiceKey       = $request->invoice_key;
        $paymentGatewayId = $request->payment_gateway_id;

        $payload = [
            "PaymentType"  => $request->payment_type,
            "SaveToken"    => $request->save_token,
            "IsRecurring"  => $request->is_recurring,
            "IntervalDays" => $request->interval_days,
            "Token"        => $request->token,
            "Bypass3DS"    => $request->bypass_3DS,
            "Card"         => [
                "Number"       => $request->card_number,
                "ExpiryMonth"  => $request->card_expiry_month,
                "ExpiryYear"   => $request->card_expiry_year,
                "SecurityCode" => $request->card_security_code
            ]
        ];

        return Myfatoorah::call("DirectPayment/$invoiceKey/$paymentGatewayId", $payload);
    }

}

<?php

namespace App\Http\Controllers\Api\User\Order\Transaction;

use DB;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\External\Myfatoorah\Myfatoorah;

class InitiateTransaction extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'  => 'required|exists:users,id',
            'jwt'      => 'required|exists:users,jwt,id,'.$request->user_id,
            'order_id' => 'required|exists:orders,id,user_id,'.$request->user_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $order = DB::table('orders')->where('id', $request->order_id)->first();

        $payload = [
            'InvoiceAmount' => $order->total_amount,
            'CurrencyIso'   => 'SAR'
        ];

        return Myfatoorah::call('InitiatePayment', $payload);
    }
}

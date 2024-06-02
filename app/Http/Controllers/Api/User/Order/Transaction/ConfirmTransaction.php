<?php

namespace App\Http\Controllers\Api\User\Order\Transaction;

use DB;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\External\Myfatoorah\Myfatoorah;

class ConfirmTransaction extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'  => 'required|exists:users,id',
            'jwt'      => 'required|exists:users,jwt,id,'.$request->user_id,
            'order_id' => 'required|exists:orders,id,user_id,'.$request->user_id,
            'key'      => 'required',
            'key_type' => 'required|in:InvoiceId,PaymentId',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $order = DB::table('orders')->where('id', $request->order_id)->first();

        $payload = [
            'Key'     => $request->key,
            'KeyType' => $request->key_type
        ];

        $result = Myfatoorah::call('GetPaymentStatus', $payload);
        $invoiceStatus = $result['Data']['InvoiceStatus'];
        $customerReference = $result['Data']['CustomerReference'];

        if($invoiceStatus != 'Paid' || $customerReference != $request->order_id) {
            return [
                'status'      => 'failed',
                'msg'         => 'Bad Transaction',
                'transaction' => $result
            ];
        }

        // TODO: Add transaction to DB & set order status as paid

        return [
            'status'      => 'success',
            'msg'         => 'Transaction Saved',
            'transaction' => $result
        ];
    }
}

<?php

namespace App\Http\Controllers\Api\User\Order\Transaction;

use DB;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\External\Myfatoorah\Myfatoorah;

class GetStatus extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'  => 'required|exists:users,id',
            'jwt'      => 'required|exists:users,jwt,id,'.$request->user_id,
            'key'      => 'required',
            'key_type' => 'required|in:InvoiceId,PaymentId',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $payload = [
            'Key'     => $request->key,
            'KeyType' => $request->key_type
        ];

        return Myfatoorah::call('GetPaymentStatus', $payload);
    }
}

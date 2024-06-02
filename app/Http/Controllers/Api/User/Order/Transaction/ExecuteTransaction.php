<?php

namespace App\Http\Controllers\Api\User\Order\Transaction;

use DB;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\External\Myfatoorah\Myfatoorah;

class ExecuteTransaction extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'        => 'required|exists:users,id',
            'jwt'            => 'required|exists:users,jwt,id,'.$request->user_id,
            'order_id'       => 'required|exists:orders,id,user_id,'.$request->user_id,
            'payment_method' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $lang = $request->has('lang') ? $request->lang : 'EN';

        $user = DB::table('users')->where('id', $request->user_id)->first();

        $order = DB::table('orders')->where('id', $request->order_id)->first();

        $payload = [
            "PaymentMethodId"    => $request->payment_method,
            "CustomerName"       => $user->en_name,
            "DisplayCurrencyIso" => "SAR",
            "MobileCountryCode"  => "+966",
            "CustomerMobile"     => $user->phone,
            "CustomerEmail"      => $user->email,
            "InvoiceValue"       => $order->total_amount,
            "CallBackUrl"        => url('api/user/order/transaction/callback/success'),
            "ErrorUrl"           => url('api/user/order/transaction/failure'),
            "Language"           => strtoupper($lang),
            "CustomerReference"  => $order->id,
            // "CustomerCivilId"    => "string",
            // "UserDefinedField"   => "string",
            "ExpiryDate"         => Carbon::now()->addHour()->toIso8601String(),
            // "SupplierCode"       => 0,
            // "SupplierValue"      => 0,
            // "SourceInfo"         => "string",
            // "CustomerAddress"    => [
            //     "Block"               => "string",
            //     "Street"              => "string",
            //     "HouseBuildingNo"     => "string",
            //     "Address"             => "string",
            //     "AddressInstructions" => "string"
            // ],
        ];
        // return $payload;

        return Myfatoorah::call('ExecutePayment', $payload);
    }
}

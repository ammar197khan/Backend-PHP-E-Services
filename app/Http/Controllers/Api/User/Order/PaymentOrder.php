<?php

namespace App\Http\Controllers\Api\User\Order;

use App\Models\Payment;
use App\Models\PushNotify;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentOrder extends Controller
{
    public function __invoke(Request $request)
    {
        $payment = PushNotify::payment($request->Key,$request->KeyType);

        if($payment->InvoiceStatus == 'paid' && $payment->InvoiceValue != 0 && $payment->PaidCurrency == 'SAR'){
            Payment::create([
                'user_id'               => $request->user_id,
                'order_id'              => $request->order_id,
                'transaction_id'        => $request->Key,
                'payment_type '         => $request->payment_type ,
                'online_payment_type '  => $request->online_payment_type ,
                'paid_at '              => Carbon::now(),
            ]);
        }

        return 'success';
    }
}

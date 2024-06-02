<?php

namespace App\Http\Controllers\Api\User\Order\Transaction;

use DB;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\External\Myfatoorah\Myfatoorah;

class CallBackUrl extends Controller
{
    public function __invoke(Request $request)
    {

        return 'Transaction is Paid';

        $payload = [
            'Key'     => $request->key,
            'KeyType' => $request->key_type
        ];

        $result = Myfatoorah::call('GetPaymentStatus', $payload);

        if($result['InvoiceStatus'] == 'Paid') {
            // TODO: Add transaction to DB & set order status as paid
        }

        return 'Transaction is ' . $result['InvoiceStatus'];
    }
}

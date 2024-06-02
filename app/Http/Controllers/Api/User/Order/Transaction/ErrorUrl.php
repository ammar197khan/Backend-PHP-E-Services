<?php

namespace App\Http\Controllers\Api\User\Order\Transaction;

use DB;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\External\Myfatoorah\Myfatoorah;

class ErrorUrl extends Controller
{
    public function __invoke(Request $request)
    {
        // Log::error('Failure Payment');
        return 'Payment has been failed.';
    }
}

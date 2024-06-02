<?php

namespace App\Http\Controllers\Api;

use Artisan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class HealthController extends Controller
{
    public function ok()
    {
        return 'ok';
    }

    public function testMail(Request $request)
    {
        Artisan::call('config:cache');

        if($request->pwd != '789789') {return 0;}
        $body = "Mail Sent at: " . now();
        Mail::raw($body, function ($message){
            $message->from('qbs@qreebs.com')
            ->subject('Qreeb - Test Mail')
            ->to('qareeb.apps@gmail.com');
        });
    }
}

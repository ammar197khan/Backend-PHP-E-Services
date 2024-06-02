<?php

namespace App\Http\Controllers\Api\User\Info;

use App\Models\Privacy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserAppPrivacy extends Controller
{
    public function __invoke(Request $request, $lang)
    {
        $text = Privacy::select($lang.'_text as text')->first();
        return response()->json($text);
    }
}

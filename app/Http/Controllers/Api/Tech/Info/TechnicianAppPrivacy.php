<?php

namespace App\Http\Controllers\Api\Tech\Info;

use App\Models\Privacy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TechnicianAppPrivacy extends Controller
{
    public function __invoke(Request $request, $lang)
    {
        $text = Privacy::select($lang.'_text as text')->first();
        return response()->json($text);
    }
}

<?php

namespace App\Http\Controllers\Api\Tech\Info;

use App\Models\AboutUs;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TechnicianAppAboutUs extends Controller
{
    public function __invoke(Request $request, $lang)
    {
        $text = AboutUs::select($lang.'_text as text')->first();
        return response()->json($text);
    }
}

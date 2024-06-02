<?php

namespace App\Http\Controllers\Api\Tech\Info;

use App\Models\Term;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TechnicianAppTerms extends Controller
{
    public function __invoke(Request $request, $lang)
    {
        $text = Term::select($lang.'_text as text')->first();
        return response()->json($text);
    }
}

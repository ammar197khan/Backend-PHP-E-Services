<?php

namespace App\Http\Controllers\Api\User\Info;

use App\Models\Term;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserAppTerms extends Controller
{
    public function __invoke(Request $request, $lang)
    {
        $text = Term::select($lang.'_text as text')->first();
        return response()->json($text);
    }
}

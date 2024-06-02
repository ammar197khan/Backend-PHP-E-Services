<?php

namespace App\Http\Controllers\Api\User\Complaint;

use App\Models\ComplainTitle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GetComplaintTypes extends Controller
{
    public function __invoke(Request $request, $lang)
    {
        $titles = ComplainTitle::select('id',$lang.'_title as title')->get();
        return response()->json($titles);
    }
}

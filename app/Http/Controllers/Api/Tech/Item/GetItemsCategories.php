<?php

namespace App\Http\Controllers\Api\Tech\Item;

use Validator;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GetItemsCategories extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tech_id'   => 'required|exists:technicians,id',
            'jwt'       => 'required|exists:technicians,jwt,id,'.$request->tech_id,
            'parent_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $lang = $request->header('lang');

        if ($request->parent_id == 0) {
            $categories = Category::where('parent_id', null)->select('id', $lang.'_name as name')->get();
        } else {
            $categories = Category::where('parent_id', $request->parent_id)->select('id', $lang.'_name as name')->get();
        }

        return response()->json($categories);
    }
}

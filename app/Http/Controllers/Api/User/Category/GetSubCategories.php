<?php

namespace App\Http\Controllers\Api\User\Category;

use Validator;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GetSubCategories extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parent_id' => 'required|exists:categories,id'
        ]);

        if($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $lang = $request->header('lang');

        // TODO: FILTER CATS WITH SERVICE PROVIDER SUBSCIBTIONS TOO

        $sub_cats =
        Category::where('parent_id', $request->parent_id)
        ->select('id', $lang . '_name as name', 'image')
        ->get();

        foreach ($sub_cats as $category) {
            $category['image'] = 'http://'.$_SERVER['SERVER_NAME'].'/categories/'.$category->image;
        }

        return response()->json($sub_cats);
    }
}

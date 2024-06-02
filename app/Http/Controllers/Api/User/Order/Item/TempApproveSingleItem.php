<?php

namespace App\Http\Controllers\Api\User\Order\Item;

use Validator;
use Illuminate\Http\Request;
use App\Models\OrderItemUser;
use App\Http\Controllers\Controller;

class TempApproveSingleItem extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'    => 'required|exists:users,id',
            'jwt'        => 'required|exists:users,jwt,id,'.$request->user_id,
            'order_id'   => 'required|exists:orders,id,user_id,'.$request->user_id,
            'request_id' => 'required|exists:order_item_users,id,order_id,'.$request->order_id,
            'status'     => 'required|in:confirmed,declined'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        OrderItemUser::findOrFail($request->request_id)->update([
            'status' => $request->status
        ]);

        return response()->json(msg($request, success(), 'success'));
    }
}

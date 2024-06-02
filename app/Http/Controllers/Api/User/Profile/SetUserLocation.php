<?php

namespace App\Http\Controllers\Api\User\Profile;

use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SetUserLocation extends Controller
{
    public function __invoke(Request $request)
    {
      $validator = Validator::make($request->all(), [
          'user_id' => 'required|exists:users,id',
          'jwt'     => 'required|exists:users,jwt,id,'.$request->user_id,
          'lat'     => 'required',
          'lng'     => 'required',
      ]);

      if($validator->fails()) {
          return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
      }

      $user = User::findOrFail($request->user_id)->first();
      $user->lat = $request->lat;
      $user->lng = $request->lng;
      $user->save();

      return response()->json(msg($request,success(),'updated'));
    }
}

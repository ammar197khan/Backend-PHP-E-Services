<?php

namespace App\Http\Controllers\Api\Tech\Auth;

use Validator;
use App\Models\TechToken;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TechnicianLogout extends Controller
{
    public function __invoke(Request $request)
    {
      $validator = Validator::make($request->all(), [
          'tech_id' => 'required|exists:technicians,id',
          'jwt'     => 'required|exists:technicians,jwt,id,'.$request->tech_id,
          'token'   => 'required',
      ]);

      if ($validator->fails()) {
          return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
      }

      TechToken::where('tech_id',$request->tech_id)
      ->where('token', $request->token)
      ->delete();

      return response()->json(['status' => 'success', 'msg' => 'updated']);
    }
}

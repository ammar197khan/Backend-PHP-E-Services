<?php

namespace App\Http\Controllers\Api\Tech\Order;

use Validator;
use Carbon\Carbon;
use App\Models\OrderTeamLeadReport;
use App\Models\Technician;
use App\Models\Collaboration;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GetAssessmentReportLog extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tech_id' => 'required|exists:technicians,id',
            'jwt'     => 'required|exists:technicians,jwt,id,'.$request->tech_id,
            'order_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }
        // GET PROVIDER PARTNERS
         $lang = $request->header('lang');
         $orderTeamLeadReport = OrderTeamLeadReport::where('order_id', $request->order_id)->get();
         $data = array();
         foreach($orderTeamLeadReport as $object){
            $data[] = [
                'order_id' => $object->order_id,
                'report_status' => $object->is_approved_status,
                'tl_remarks' => $object->tl_remarks,
                'tl_submit_date' => $object->tl_submit_date,
                'supervisor_remarks' => $object->supervisor_remarks,
                'supervisor_send_back_date' => $object->supervisor_sendback_date
            ];
         }
        return response()->json(['assessment_report_log' => $data]);
    }
}

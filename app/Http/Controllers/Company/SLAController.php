<?php

namespace App\Http\Controllers\Company;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Sla;
use App\Models\CompanySubscription;
use App\Models\Category;
use DateTime;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class SLAController extends Controller
{
    protected $SlaModel;
    public function __construct(){
        $SlaModel = new Sla();
        $this->SlaModel  = $SlaModel;
    }
    public function index(Request $request)
    {
         $slas =  $this->SlaModel->where('provider_id', Company()->company_id)->with(['sub_cats' => function($q){
            $q->with('parent');
         }])->get();
         $slas = collect($slas)->toArray();
        return view('company.sla.index', compact('slas'));
    }
    function update(Request $request){
      $slaModel =  $this->SlaModel->where('id', $request->id)->first();
        $response_time = $request->response_hour .':'.  $request->response_minute;
      $request->request->add(['response_time' => $response_time]);

        $assessment_time = $request->assessment_hour .':'.  $request->assessment_minute;
      $request->request->add(['assessment_time' => $assessment_time]);

        $rectification_time = $request->rectification_hour .':'. $request->rectification_minute;
      $request->request->add(['rectification_time' => $rectification_time]);

      if($slaModel){
        $this->SlaModel->saveOrUpdate($slaModel, $request);
      }
      $response['message'] = 'success';
      $response['status'] = true;
      print_r(json_encode($response));
      die;


    }
    public function calculateTime($firstTime, $secondTime, $slaTime){

        $result = false;

        $dateDiff = intval((strtotime($secondTime)-strtotime($firstTime))/60);

        $hours = intval($dateDiff/60);
        $hours = sprintf("%02d", (int)$hours);
        $minutes = $dateDiff%60;
        $minutes = sprintf("%02d", (int)$minutes);
        $time =  "$hours:$minutes";
        $slaTime = explode(':', $slaTime);
        $slaHr = (int)$slaTime['0'];
        $slaMin = (int)$slaTime['1'];

        if($hours > $slaHr){
           $result =true;
        }
        if($hours == $slaHr ){

            if($minutes > $slaMin){
                $result =true;
            }
        }
      return array([$result, $time]);

    }
    public function slaTimeBreach($track, $slaTime, $trackForArray){
        $trackTimeFor= $trackForArray;
         $filteredTime = array_filter(
            $track,
            function ($v, $k) use ($trackTimeFor) {

                return in_array($v['status'], $trackTimeFor);
            },
            ARRAY_FILTER_USE_BOTH
        );
        $filteredTime = array_values($filteredTime);
      ;
       $calculateTime = [[false, '00:00']];
       if(!empty($filteredTime['1']['date']) && !empty($filteredTime['0']['date'])){
        $calculateTime   = $this->calculateTime($filteredTime['0']['date'], $filteredTime['1']['date'], $slaTime );
       }
        return $calculateTime;
    }

    public function getPagination(Request $request, $data){
        $page = 1;
        if(!empty($request->page)){
            $page = $request->page;
        }
        $arrayRequest =  !empty($request->sort) ? explode(".",$request->sort) : '' ;
        $field = '';
        $sort  = '';
         if(!empty($arrayRequest)){
            $field =  $arrayRequest ['0'];
            $sort =  $arrayRequest  ['1'];
         }
         $data = collect($data);
         if(!empty($sort) && $sort == 'desc'){
            $data = $data->sortByDesc($field);
         }elseif(!empty($sort) && $sort == 'asc'){
            $data = $data->sortBy($field);
         }

        $data = collect($data)->toArray();

        $pageStart           = $page;
        $perPage = 10;
        $offSet              = ($pageStart * $perPage) - $perPage;
        $itemsForCurrentPage = array_slice($data, $offSet, $perPage, TRUE);

        $data = new LengthAwarePaginator(
            $itemsForCurrentPage, count($data), $perPage,
            Paginator::resolveCurrentPage(),
            ['path' => Paginator::resolveCurrentPath()]
        );


        return $data;
    }

    public function orderDashboard(Request $request){

        $view = '';
        if(!empty($request->view)){
            $view = $request->view;
        }

        $subs    = CompanySubscription::where('company_id', company()->company_id)->first();
        $cat_ids = Category::whereIn('id', isset($subs) ? unserialize($subs->subs) : [])->pluck('parent_id');
        $cats    = Category::whereIn('id', $cat_ids)->select('id','en_name')->get();
       $arrayRequest =  !empty($request->sort) ? explode(".",$request->sort) : '' ;
       $field = '';
       $sort  = '';
        if(!empty($arrayRequest)){
           $field =  $arrayRequest ['0'];
           $sort =  $arrayRequest  ['1'];
        }
        $companyId = Company()->company_id;
        $orders = Order::where('company_id', $companyId)->with(['track', 'user_details'])->withWhereHas('sla' , function($q) use($companyId){
           $q->where('provider_id', $companyId);
        })->orderBy('id','desc')->get();
        $data = array();
        foreach(collect($orders)->toArray() as $dataOrder){

            $type = !empty($dataOrder['type'])? $dataOrder['type'] : '';
            if($type == 're_scheduled'){
                $type = 'scheduled';
            }
            $sla  =  !empty($dataOrder['sla'])? $dataOrder['sla'] : array();
            $filteredSla = array_filter(
                $sla,
                function ($v, $k) use ($type) {
                    return $v['request_type'] == $type;
                },
                ARRAY_FILTER_USE_BOTH
            );
            $filteredSlaResponse_time = !empty(array_values($filteredSla)['0']) && !empty(array_values($filteredSla)['0']['response_time']) ? array_values($filteredSla)['0']['response_time'] : '00:00';
            $filteredSlaAssessment_time = !empty(array_values($filteredSla)['0']) && !empty(array_values($filteredSla)['0']['assessment_time']) ? array_values($filteredSla)['0']['assessment_time'] : '00:00';
            $filteredSlaRectification_time = !empty(array_values($filteredSla)['0']) && !empty(array_values($filteredSla)['0']['rectification_time']) ? array_values($filteredSla)['0']['rectification_time'] : '00:00';

            $filteredTrackTechnician = array_filter(
                $dataOrder['track'],
                function ($v, $k) use ($type) {
                    return $v['status'] == 'Technician selected';
                },
                ARRAY_FILTER_USE_BOTH
            );
            $filteredTrackSupervisor = array_filter(
                $dataOrder['track'],
                function ($v, $k) use ($type) {
                    return $v['status'] == 'Assessor Supervisor selected';
                },
                ARRAY_FILTER_USE_BOTH
            );
            if(!empty($filteredTrackTechnician)){
                $breach_response_time_arr = array('Service request' , 'Technician On the Way');
                $breach_assessment_time_arr = array('Technician On the Way' , 'Maintenance In Progress');
                $breach_rectification_time_arr = array('Maintenance In Progress' , 'Job Completed');

            }else if(!empty($filteredTrackSupervisor)){
                $breach_response_time_arr = array('Service request' , 'Assessor On the Way');
                $breach_assessment_time_arr = array('Assessor On the Way' , 'Assigned to Technician');
                $breach_rectification_time_arr = array('Assigned to Technician' , 'Job Completed');

            }

            $data[] =
            [
                'id'  => !empty($dataOrder['id'])? $dataOrder['id'] : '',
                'type'  => !empty($dataOrder['type'])? $dataOrder['type'] : '',
                'completed'  => !empty($dataOrder['completed'])? $dataOrder['completed']: '',
                'company_id' => !empty($dataOrder['company_id'])? $dataOrder['company_id']: '',
                'date' => !empty($dataOrder['created_at'])? $dataOrder['created_at']: '',
                'service_type' => !empty($dataOrder['service_type'])? $dataOrder['service_type']: '',
                'cat_id' => !empty($dataOrder['cat_id'])? Order::get_category('en',$dataOrder['cat_id']) : '',
                'sub_cat_id' => !empty($dataOrder['sub_cat_id'])? Order::get_category('en',$dataOrder['sub_cat_id']): '',
                'desc'           => !empty($dataOrder['user_details']) && !empty($dataOrder['user_details']['desc'])? $dataOrder['user_details']['desc'] : '',
                'response_time'           => !empty($dataOrder['track']) && !empty($dataOrder['sla'])  && !empty($dataOrder['sla'])?  $this->slaTimeBreach($dataOrder['track'], $filteredSlaResponse_time, $breach_response_time_arr)['0']['1'] : '',
                'assessment_time'           => !empty($dataOrder['track']) && !empty($dataOrder['sla'])  && !empty($dataOrder['sla']) ? $this->slaTimeBreach($dataOrder['track'], $filteredSlaAssessment_time, $breach_assessment_time_arr)['0']['1'] : '',
                'rectification_time'           => !empty($dataOrder['track']) && !empty($dataOrder['sla']) && !empty($dataOrder['sla']) ? $this->slaTimeBreach($dataOrder['track'],$filteredSlaRectification_time, $breach_rectification_time_arr)['0']['1'] : '',
                'breach_response_time'           =>  !empty($dataOrder['track']) && !empty($dataOrder['sla'])  && !empty($dataOrder['sla'])?  ($this->slaTimeBreach($dataOrder['track'], $filteredSlaResponse_time, $breach_response_time_arr)['0']['0'] == false ? 'N' : 'Y') : '',
                'breach_assessment_time'           => !empty($dataOrder['track']) && !empty($dataOrder['sla'])  && !empty($dataOrder['sla']) ? ($this->slaTimeBreach($dataOrder['track'], $filteredSlaAssessment_time, $breach_assessment_time_arr)['0']['0'] == false ? 'N' : 'Y'): '',
                'breach_rectification_time'           => !empty($dataOrder['track']) && !empty($dataOrder['sla']) && !empty($dataOrder['sla']) ? ($this->slaTimeBreach($dataOrder['track'],$filteredSlaRectification_time, $breach_rectification_time_arr)['0']['0'] == false ? 'N' : 'Y'): '',
            ];


        }

        $breachResponseTime = array_filter($data, function($v, $k) {
            return $v['breach_response_time'] == 'Y';
          }, ARRAY_FILTER_USE_BOTH);
          $breachAssessmentTime = array_filter($data, function($v, $k) {
            return $v['breach_assessment_time'] == 'Y';
          }, ARRAY_FILTER_USE_BOTH);
          $breachRectificationTime = array_filter($data, function($v, $k) {
            return $v['breach_rectification_time'] == 'Y';
          }, ARRAY_FILTER_USE_BOTH);
          $workOrderOpen = array_filter($data, function($v, $k) {
            return $v['completed'] == 0;
          }, ARRAY_FILTER_USE_BOTH);
          $workOrderClosed = array_filter($data, function($v, $k) {
            return $v['completed'] == 1;
          }, ARRAY_FILTER_USE_BOTH);

          if($view == 'response-sla-breach'){
                $data =  $this->getPagination($request, $breachResponseTime);
            return view('company.sla.dashboards.response-sla-breach', compact('data', 'breachResponseTime', 'breachAssessmentTime','breachRectificationTime', 'workOrderOpen', 'workOrderClosed', 'cats'));
         }elseif($view == 'assessment-sla-breach'){
            $data =  $this->getPagination($request, $breachAssessmentTime);
            return view('company.sla.dashboards.assessment-sla-breach', compact('data', 'breachResponseTime', 'breachAssessmentTime','breachRectificationTime', 'workOrderOpen', 'workOrderClosed', 'cats'));
         }elseif($view == 'rectification-sla-breach'){
            $data =  $this->getPagination($request, $breachRectificationTime);
            return view('company.sla.dashboards.rectification-sla-breach', compact('data', 'breachResponseTime', 'breachAssessmentTime','breachRectificationTime', 'workOrderOpen', 'workOrderClosed', 'cats'));
         }elseif($view == 'work-order-open'){
            $data =  $this->getPagination($request,  $workOrderOpen);
            return view('company.sla.dashboards.work-order-open', compact('data', 'breachResponseTime', 'breachAssessmentTime','breachRectificationTime', 'workOrderOpen', 'workOrderClosed', 'cats'));
         }elseif($view == 'work-order-close'){
            $data =  $this->getPagination($request,  $workOrderClosed);
            return view('company.sla.dashboards.work-order-closed', compact('data', 'breachResponseTime', 'breachAssessmentTime','breachRectificationTime', 'workOrderOpen', 'workOrderClosed', 'cats'));
         }
         $data =  $this->getPagination($request,  $workOrderOpen);
       return view('company.sla.dashboards.work-order-open', compact('data', 'breachResponseTime', 'breachAssessmentTime','breachRectificationTime', 'workOrderOpen', 'workOrderClosed', 'cats'));
    }
    public function filtersOrderDashboard(Request $request, $collection){
        $cat_id = '';
        $date = '';
        $type = '';
        $sub_cat_id = '';
        $response_time = '';
        $assessment_time = '';
        $rectification_time = '';
        $breach_response_time = '';
        $breach_assessment_time = '';
        $breach_rectification_time = '';
        $search = '';
        if(!empty($request->cat_id)){
            $cat_id = $request->cat_id;
        }
        if(!empty($request->date)){
            $date = $request->date;
        }
        if(!empty($request->type)){
            $type = $request->type;
        }
        if(!empty($request->search)){
            $search = $request->search;
        }
        if(!empty($request->sub_cat_id)){
            $sub_cat_id = $request->sub_cat_id;
        }
        if(!empty($request->response_time)){
            $response_time = $request->response_time;
        }
        if(!empty($request->assessment_time)){
            $assessment_time = $request->assessment_time;
        }
        if(!empty($request->rectification_time)){
            $rectification_time = $request->rectification_time;
        }
        if(!empty($request->breach_response_time)){
            $breach_response_time = $request->breach_response_time;
        }
        if(!empty($request->breach_assessment_time)){
            $breach_assessment_time = $request->breach_assessment_time;
        }
        if(!empty($request->breach_rectification_time)){
            $breach_rectification_time = $request->breach_rectification_time;
        }

        if($date){
            $collection =  $collection->filter(function ($item) use($date){
                return  Carbon::parse($date)->format('d-m-Y') ==   Carbon::parse($item['date'])->format('d-m-Y');
            });
        }
        if($type){
            $collection =  $collection->filter(function ($item) use($type){
                return $type == $item['type'];
            });
        }
        if($sub_cat_id){
            $collection =  $collection->filter(function ($item) use($sub_cat_id){
                return  $sub_cat_id  == $item['sub_cat_id'];
            });
        }
        if($response_time){
            $collection =  $collection->filter(function ($item) use($response_time){
                return  $response_time  == $item['response_time'];
            });
        }

        if($assessment_time){
            $collection =  $collection->filter(function ($item) use($assessment_time){
                return  $assessment_time  == $item['assessment_time'];
            });
        }
        if($rectification_time){
            $collection =  $collection->filter(function ($item) use($rectification_time){
                return  $rectification_time  == $item['rectification_time'];
            });
        }


        if($breach_response_time){
            $collection =  $collection->filter(function ($item) use($breach_response_time){
                return  $breach_response_time  == $item['breach_response_time'];
            });
        }
        if($breach_assessment_time){
            $collection =  $collection->filter(function ($item) use($breach_assessment_time){
                return  $breach_assessment_time  == $item['breach_assessment_time'];
            });
        }
        if($breach_rectification_time){
            $collection =  $collection->filter(function ($item) use($breach_rectification_time){
                return  $breach_rectification_time  == $item['breach_rectification_time'];
            });
        }

        if($search){

            $collection =  $collection->filter(function ($item) use($search){

                return false !== stripos($item['id'], $search) || false !== stripos($item['type'], $search) || false !== stripos($item['cat_id'], $search) || false !== stripos($item['sub_cat_id'], $search) || false !== stripos($item['response_time'], $search)
                || false !== stripos($item['assessment_time'], $search)
                || false !== stripos($item['rectification_time'], $search)
                || false !== stripos($item['breach_response_time'], $search)
                || false !== stripos($item['breach_assessment_time'], $search)
                || false !== stripos($item['breach_rectification_time'], $search);

            });

        }
          return collect($collection);

    }

    public function searchOrderDashboard(Request $request){
        $view = '';
        if(!empty($request->view)){
            $view = $request->view;
        }
        $subs    = CompanySubscription::where('company_id', company()->company_id)->first();
        $cat_ids = Category::whereIn('id', isset($subs) ? unserialize($subs->subs) : [])->pluck('parent_id');
        $cats    = Category::whereIn('id', $cat_ids)->select('id','en_name')->get();

        $companyId = Company()->company_id;
        $orders = Order::where('company_id', $companyId)->whereNotIn('type', array('canceled'))->with(['track', 'user_details', 'sla'])->withWhereHas('sla' , function($q) use($companyId){
           $q->where('provider_id', $companyId);
        })->orderBy('created_at','desc')->get();
        $data = array();
        foreach(collect($orders)->toArray() as $dataOrder){

            $type = !empty($dataOrder['type'])? $dataOrder['type'] : '';
            if($type == 're_scheduled'){
                $type = 'scheduled';
            }

            $sla  =  !empty($dataOrder['sla'])? $dataOrder['sla'] : array();
            $filteredSla = array_filter(
                $sla,
                function ($v, $k) use ($type) {
                    return $v['request_type'] == $type;
                },
                ARRAY_FILTER_USE_BOTH
            );
            $filteredSlaResponse_time = !empty(array_values($filteredSla)['0']) && !empty(array_values($filteredSla)['0']['response_time']) ? array_values($filteredSla)['0']['response_time'] : '00:00';
            $filteredSlaAssessment_time = !empty(array_values($filteredSla)['0']) && !empty(array_values($filteredSla)['0']['assessment_time']) ? array_values($filteredSla)['0']['assessment_time'] : '00:00';
            $filteredSlaRectification_time = !empty(array_values($filteredSla)['0']) && !empty(array_values($filteredSla)['0']['rectification_time']) ? array_values($filteredSla)['0']['rectification_time'] : '00:00';
            $filteredTrackTechnician = array_filter(
                $dataOrder['track'],
                function ($v, $k) use ($type) {
                    return $v['status'] == 'Technician selected';
                },
                ARRAY_FILTER_USE_BOTH
            );
            $filteredTrackSupervisor = array_filter(
                $dataOrder['track'],
                function ($v, $k) use ($type) {
                    return $v['status'] == 'Assessor Supervisor selected';
                },
                ARRAY_FILTER_USE_BOTH
            );
            if(!empty($filteredTrackTechnician)){
                $breach_response_time_arr = array('Service request' , 'Technician On the Way');
                $breach_assessment_time_arr = array('Technician On the Way' , 'Maintenance In Progress');
                $breach_rectification_time_arr = array('Maintenance In Progress' , 'Job Completed');

            }else if(!empty($filteredTrackSupervisor)){
                $breach_response_time_arr = array('Service request' , 'Assessor On the Way');
                $breach_assessment_time_arr = array('Assessor On the Way' , 'Assigned to Technician');
                $breach_rectification_time_arr = array('Assigned to Technician' , 'Job Completed');

            }

            $data[] =
            [
                'id'  => !empty($dataOrder['id'])? $dataOrder['id'] : '',
                'type'  => !empty($dataOrder['type'])? $dataOrder['type'] : '',
                'completed'  => !empty($dataOrder['completed'])? $dataOrder['completed']: '',
                'company_id' => !empty($dataOrder['company_id'])? $dataOrder['company_id']: '',
                'date' => !empty($dataOrder['created_at'])? $dataOrder['created_at']: '',
                'service_type' => !empty($dataOrder['service_type'])? $dataOrder['service_type']: '',
                'cat_id' => !empty($dataOrder['cat_id'])? Order::get_category('en',$dataOrder['cat_id']) : '',
                'sub_cat_id' => !empty($dataOrder['sub_cat_id'])? Order::get_category('en',$dataOrder['sub_cat_id']): '',
                'desc'           => !empty($dataOrder['user_details']) && !empty($dataOrder['user_details']['desc'])? $dataOrder['user_details']['desc'] : '',
                'response_time'           => !empty($dataOrder['track']) && !empty($dataOrder['sla'])  && !empty($dataOrder['sla'])?  $this->slaTimeBreach($dataOrder['track'], $filteredSlaResponse_time, $breach_response_time_arr)['0']['1'] : '',
                'assessment_time'           => !empty($dataOrder['track']) && !empty($dataOrder['sla'])  && !empty($dataOrder['sla']) ? $this->slaTimeBreach($dataOrder['track'], $filteredSlaAssessment_time, $breach_assessment_time_arr)['0']['1'] : '',
                'rectification_time'           => !empty($dataOrder['track']) && !empty($dataOrder['sla']) && !empty($dataOrder['sla']) ? $this->slaTimeBreach($dataOrder['track'],$filteredSlaRectification_time, $breach_rectification_time_arr)['0']['1'] : '',
                'breach_response_time'           =>  !empty($dataOrder['track']) && !empty($dataOrder['sla'])  && !empty($dataOrder['sla'])?  ($this->slaTimeBreach($dataOrder['track'], $filteredSlaResponse_time, $breach_response_time_arr)['0']['0'] == false ? 'N' : 'Y') : '',
                'breach_assessment_time'           => !empty($dataOrder['track']) && !empty($dataOrder['sla'])  && !empty($dataOrder['sla']) ? ($this->slaTimeBreach($dataOrder['track'], $filteredSlaAssessment_time, $breach_assessment_time_arr)['0']['0'] == false ? 'N' : 'Y'): '',
                'breach_rectification_time'           => !empty($dataOrder['track']) && !empty($dataOrder['sla']) && !empty($dataOrder['sla']) ? ($this->slaTimeBreach($dataOrder['track'],$filteredSlaRectification_time, $breach_rectification_time_arr)['0']['0'] == false ? 'N' : 'Y'): '',
            ];

        }
        $breachResponseTime = array_filter($data, function($v, $k) {
            return $v['breach_response_time'] == 'Y';
          }, ARRAY_FILTER_USE_BOTH);
          $breachAssessmentTime = array_filter($data, function($v, $k) {
            return $v['breach_assessment_time'] == 'Y';
          }, ARRAY_FILTER_USE_BOTH);
          $breachRectificationTime = array_filter($data, function($v, $k) {
            return $v['breach_rectification_time'] == 'Y';
          }, ARRAY_FILTER_USE_BOTH);
          $workOrderOpen = array_filter($data, function($v, $k) {
            return $v['completed'] == 0;
          }, ARRAY_FILTER_USE_BOTH);
          $workOrderClosed = array_filter($data, function($v, $k) {
            return $v['completed'] == 1;
          }, ARRAY_FILTER_USE_BOTH);



          if($view == 'response-sla-breach'){
            $breachResponseTime =  $this->filtersOrderDashboard($request,  collect($breachResponseTime));
            $data =  $this->getPagination($request, $breachResponseTime);
        return view('company.sla.dashboards.response-sla-breach', compact('data', 'breachResponseTime', 'breachAssessmentTime','breachRectificationTime', 'workOrderOpen', 'workOrderClosed', 'cats'));
     }elseif($view == 'assessment-sla-breach'){
        $breachAssessmentTime =  $this->filtersOrderDashboard($request,  collect($breachAssessmentTime));
        $data =  $this->getPagination($request, $breachAssessmentTime);
        return view('company.sla.dashboards.assessment-sla-breach', compact('data', 'breachResponseTime', 'breachAssessmentTime','breachRectificationTime', 'workOrderOpen', 'workOrderClosed', 'cats'));
     }elseif($view == 'rectification-sla-breach'){
        $breachRectificationTime =  $this->filtersOrderDashboard($request,  collect($breachRectificationTime));
        $data =  $this->getPagination($request, $breachRectificationTime);
        return view('company.sla.dashboards.rectification-sla-breach', compact('data', 'breachResponseTime', 'breachAssessmentTime','breachRectificationTime', 'workOrderOpen', 'workOrderClosed', 'cats'));
     }elseif($view == 'work-order-open'){
        $workOrderOpen =  $this->filtersOrderDashboard($request,  collect($workOrderOpen));
        $data =  $this->getPagination($request,  $workOrderOpen);
        return view('company.sla.dashboards.work-order-open', compact('data', 'breachResponseTime', 'breachAssessmentTime','breachRectificationTime', 'workOrderOpen', 'workOrderClosed', 'cats'));
     }elseif($view == 'work-order-close' || empty($view)){
        $workOrderClosed =  $this->filtersOrderDashboard($request,  collect($workOrderClosed));
        $data =  $this->getPagination($request,  $workOrderClosed);
        return view('company.sla.dashboards.work-order-closed', compact('data', 'breachResponseTime', 'breachAssessmentTime','breachRectificationTime', 'workOrderOpen', 'workOrderClosed', 'cats'));
     }
     $data =  $this->getPagination($request,  $workOrderOpen);
   return view('company.sla.dashboards.work-order-open', compact('data', 'breachResponseTime', 'breachAssessmentTime','breachRectificationTime', 'workOrderOpen', 'workOrderClosed', 'cats'));

    }




}

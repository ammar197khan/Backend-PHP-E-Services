@php
$sorter = isset($_GET['sort']) ? explode('.', $_GET['sort'])[0] : 'id';
$direction = isset($_GET['sort']) ? explode('.', $_GET['sort'])[1] : 'asc';
$dirIcon = $direction == 'asc' ? 'desc' : 'asc';
@endphp
<style>
    .heading-row{
                            background-color: #D3D3D3;
                            color: black;
                          }
                          .table-data{
                            /* padding: 5px; */
                            font-weight: 600;
                            text-align: center;
                            font-family: system-ui;
                          }
                          .card{
                            display: flex;
                            align-items: center;
                              justify-content: start;
                              padding: 10px;
                              height: 35px;
                              border-radius: 5px;
                              width: 17%;
                              box-shadow: 2px 2px 2px 2px #888888;
                              font-size: 12px;
                              color: #3C4E5E;
                          }
                          .card-nmbr{
                            margin-left: 10px;
                            padding-right: 10px;
                            border-right: 1px solid #D3D3D3
                          }
                          .heading-row{
                            color: black;
                            background-color: #D3D3D3;
                          }
</style>
@extends('company.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/company/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>SLA</li>

    </ul>
    <!-- END BREADCRUMB -->
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
                @include('company.layouts.message')
                <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div style="display: flex; padding: 10px 10px ; margin-bottom: 20px; align-items: center; justify-content: center; gap: 20px;">
                            <div class="card"  onclick="sayHello();" style="cursor: pointer;">
                              <div class="card-nmbr">{{  count($workOrderOpen) }}</div>
                              <div style="padding-left: 3px;" onclick="gettabel('table-1');">Work Order Open</div>
                            </div>
                            <div class="card" style="cursor: pointer;"><div class="card-nmbr">{{  count($workOrderClosed) }}</div>
                              <div style="padding-left: 3px;" onclick="gettabel('table-2');">Work Order Closed</div></div>
                            <div class="card" style="cursor: pointer;"><div class="card-nmbr">{{  count($breachResponseTime) }}</div>
                              <div style="padding-left: 3px;" onclick="gettabel('table-3');">Response SLA Breach</div></div>
                            <div class="card" style="cursor: pointer;"><div class="card-nmbr">{{  count($breachAssessmentTime) }}</div>
                              <div style="padding-left: 10px;" onclick="gettabel('table-4');">Assessment SLA Breach</div></div>
                              <div class="card" style="cursor: pointer;"><div class="card-nmbr">{{  count($breachRectificationTime) }}</div>
                              <div style="padding-left: 10px;" onclick="gettabel('table-5');">Rectification SLA Breach</div></div>
                          </div>

                      </div>
                          {{-- <div style="display: flex; justify-content: center;" id="work-order-open"> --}}
                            <div style="width: 100%; overflow: auto; height: 400px;" id="work-order-open">
                            <table class="table" border-color="" style="padding-top: 20px;">
                              <tr class="heading-row" style="position: sticky; top: 0px;">
                                <th rowspan="2" class="table-data" style="border-radius: 10px 0px 0px 0px;">
                                  WO Number
                                </th>
                                <th rowspan="2" class="table-data">
                                  Date
                                </th>
                                <th rowspan="2" class="table-data">
                                  Service Type
                                </th>
                                <th rowspan="2" class="table-data">
                                  Service Category
                                </th>
                                <th rowspan="2" class="table-data">
                                  Service Sub-category
                                </th>
                                <th rowspan="2" class="table-data">
                                  Issue Description
                                </th>
                                <th rowspan="2" class="table-data">
                                  Response Time<br>
                                  (HH:MM)
                                </th>
                                <th rowspan="2" class="table-data">
                                  Assessment Time<br>
                                  (HH:MM)
                                </th>
                                <th rowspan="2" class="table-data">
                                  Rectification Time<br>
                                  (HH:MM)
                                </th>
                                <th colspan="3" class="table-data" style="border-radius: 0px 10px 0px 0px;">
                                  SLA Breach
                                </th>
                              </tr>
                              <tr class="heading-row">
                                <th class="table-data">
                                  Response
                                </th>
                                <th class="table-data">
                                  Assessment
                                </th>
                                <th class="table-data">
                                  Rectification
                                </th>
                              </tr>
                                @php
                                $i = 1;
                                @endphp
                           @foreach($workOrderOpen as $dta)
                              <tr>
                                <td class="table-data">
                                    {{  $dta['id']}}
                                </td>
                                <td class="table-data">

                                  {{  Carbon\Carbon::parse($dta['date'])->format('d-m-Y')   }}

                                </td>
                                <td class="table-data">
                                  {{ $dta['type']   }}
                                </td>
                                <td class="table-data">
                                  {{ $dta['cat_id']   }}
                                </td>
                                <td class="table-data">
                                  {{ $dta['sub_cat_id']   }}
                                </td>
                                <td class="table-data">
                                  {{ $dta['desc']   }}
                                </td>
                                <td class="table-data">
                                  {{ $dta['response_time']   }}
                                </td>
                                <td class="table-data">
                                  {{ $dta['assessment_time']   }}

                                </td>
                                <td class="table-data">
                                  {{ $dta['rectification_time']   }}
                                </td>
                                <td class="table-data">
                                  {{ $dta['breach_response_time']   }}
                                </td>
                                <td class="table-data">
                                  {{ $dta['breach_assessment_time']   }}
                                </td>
                                <td class="table-data">
                                  {{ $dta['breach_rectification_time']   }}
                                </td>
                              </tr>
                              @endforeach
                            </table>
                        </div>
                            {{-- </div> --}}
                          {{-- <div style="display: flex; justify-content: center; display:none" id="work-order-closed"> --}}
                            <div style="width: 100%; overflow: auto; display:none; height:400px;"  id="work-order-closed">
                            <table class="table" border-color="grey">
                              <tr class="heading-row" style="position: sticky; top: 0px;">
                                <th rowspan="2" class="table-data" style="border-radius: 10px 0px 0px 0px;">
                                  WO Number
                                </th>
                                <th rowspan="2" class="table-data">
                                  Date
                                </th>
                                <th rowspan="2" class="table-data">
                                  Service Type
                                </th>
                                <th rowspan="2" class="table-data">
                                  Service Category
                                </th>
                                <th rowspan="2" class="table-data">
                                  Service Sub-category
                                </th>
                                <th rowspan="2" class="table-data">
                                  Issue Description
                                </th>
                                <th rowspan="2" class="table-data">
                                  Response Time<br>
                                  (HH:MM)
                                </th>
                                <th rowspan="2" class="table-data">
                                  Assessment Time<br>
                                  (HH:MM)
                                </th>
                                <th rowspan="2" class="table-data">
                                  Rectification Time<br>
                                  (HH:MM)
                                </th>
                                <th colspan="3" class="table-data" style="border-radius: 0px 10px 0px 0px;">
                                  SLA Breach
                                </th>
                              </tr>
                              <tr class="heading-row">
                                <th class="table-data">
                                  Response
                                </th>
                                <th class="table-data">
                                  Assessment
                                </th>
                                <th class="table-data">
                                  Rectification
                                </th>
                              </tr>
                                @php
                                $i = 1;
                                @endphp
                           @foreach($workOrderClosed as $workOrderClosedDta)
                              <tr>
                                <td class="table-data">
                                    {{   $workOrderClosedDta['id'] }}
                                </td>
                                <td class="table-data">
                                  {{  Carbon\Carbon::parse($workOrderClosedDta['date'])->format('d-m-Y')   }}
                                </td>
                                <td class="table-data">
                                  {{ $workOrderClosedDta['type']   }}
                                </td>
                                <td class="table-data">
                                  {{ $workOrderClosedDta['cat_id']   }}
                                </td>
                                <td class="table-data">
                                  {{ $workOrderClosedDta['sub_cat_id']   }}
                                </td>
                                <td class="table-data">
                                  {{ $workOrderClosedDta['desc']   }}
                                </td>
                                <td class="table-data">
                                  {{ $workOrderClosedDta['response_time']   }}
                                </td>
                                <td class="table-data">
                                  {{ $workOrderClosedDta['assessment_time']   }}

                                </td>
                                <td class="table-data">
                                  {{ $workOrderClosedDta['rectification_time']   }}
                                </td>
                                <td class="table-data">
                                  {{ $workOrderClosedDta['breach_response_time']   }}
                                </td>
                                <td class="table-data">
                                  {{ $workOrderClosedDta['breach_assessment_time']   }}
                                </td>
                                <td class="table-data">
                                  {{ $workOrderClosedDta['breach_rectification_time']   }}
                                </td>
                              </tr>
                              @endforeach
                            </table>
                          </div>
                          {{-- <div style="display: flex; justify-content: center; display:none" id="response-sla-breach"> --}}
                            <div style="width: 100%; overflow: auto; display:none; height:400px;" id="response-sla-breach">
                            <table class="table" border-color="grey">
                              <tr class="heading-row" style="position: sticky; top: 0px;">
                                <th rowspan="2" class="table-data" style="border-radius: 10px 0px 0px 0px;">
                                  WO Number
                                </th>
                                <th rowspan="2" class="table-data">
                                  Date
                                </th>
                                <th rowspan="2" class="table-data">
                                  Service Type
                                </th>
                                <th rowspan="2" class="table-data">
                                  Service Category
                                </th>
                                <th rowspan="2" class="table-data">
                                  Service Sub-category
                                </th>
                                <th rowspan="2" class="table-data">
                                  Issue Description
                                </th>
                                <th rowspan="2" class="table-data">
                                  Response Time<br>
                                  (HH:MM)
                                </th>
                                <th rowspan="2" class="table-data">
                                  Assessment Time<br>
                                  (HH:MM)
                                </th>
                                <th rowspan="2" class="table-data">
                                  Rectification Time<br>
                                  (HH:MM)
                                </th>
                                <th colspan="3" class="table-data" style="border-radius: 0px 10px 0px 0px;">
                                  SLA Breach
                                </th>
                              </tr>
                              <tr class="heading-row">
                                <th class="table-data">
                                  Response
                                </th>
                                <th class="table-data">
                                  Assessment
                                </th>
                                <th class="table-data">
                                  Rectification
                                </th>
                              </tr>
                                @php
                                $i = 1;
                                @endphp
                           @foreach($breachResponseTime as $breachResponseTimeDta)
                              <tr>
                                <td class="table-data">
                                    {{   $breachResponseTimeDta['id'] }}
                                </td>
                                <td class="table-data">
                                  {{  Carbon\Carbon::parse($breachResponseTimeDta['date'])->format('d-m-Y')   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachResponseTimeDta['type']   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachResponseTimeDta['cat_id']   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachResponseTimeDta['sub_cat_id']   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachResponseTimeDta['desc']   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachResponseTimeDta['response_time']   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachResponseTimeDta['assessment_time']   }}

                                </td>
                                <td class="table-data">
                                  {{ $breachResponseTimeDta['rectification_time']   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachResponseTimeDta['breach_response_time']   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachResponseTimeDta['breach_assessment_time']   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachResponseTimeDta['breach_rectification_time']   }}
                                </td>
                              </tr>
                              @endforeach
                            </table>
                          </div>
                          {{-- <div style="display: flex; justify-content: center; display:none" id="assessment-sla-breach"> --}}
                            <div style="width: 100%; overflow: auto; display:none; height:400px;" id="assessment-sla-breach">
                            <table class="table" border-color="grey">
                              <tr class="heading-row" style="position: sticky; top: 0px;">
                                <th rowspan="2" class="table-data" style="border-radius: 10px 0px 0px 0px;">
                                  WO Number
                                </th>
                                <th rowspan="2" class="table-data">
                                  Date
                                </th>
                                <th rowspan="2" class="table-data">
                                  Service Type
                                </th>
                                <th rowspan="2" class="table-data">
                                  Service Category
                                </th>
                                <th rowspan="2" class="table-data">
                                  Service Sub-category
                                </th>
                                <th rowspan="2" class="table-data">
                                  Issue Description
                                </th>
                                <th rowspan="2" class="table-data">
                                  Response Time<br>
                                  (HH:MM)
                                </th>
                                <th rowspan="2" class="table-data">
                                  Assessment Time<br>
                                  (HH:MM)
                                </th>
                                <th rowspan="2" class="table-data">
                                  Rectification Time<br>
                                  (HH:MM)
                                </th>
                                <th colspan="3" class="table-data" style="border-radius: 0px 10px 0px 0px;">
                                  SLA Breach
                                </th>
                              </tr>
                              <tr class="heading-row">
                                <th class="table-data">
                                  Response
                                </th>
                                <th class="table-data">
                                  Assessment
                                </th>
                                <th class="table-data">
                                  Rectification
                                </th>
                              </tr>
                                @php
                                $i = 1;
                                @endphp
                           @foreach($breachAssessmentTime as $breachAssessmentTimeDta)
                              <tr>
                                <td class="table-data">
                                    {{  $breachAssessmentTimeDta['id'] }}
                                </td>
                                <td class="table-data">
                                  {{  Carbon\Carbon::parse($breachAssessmentTimeDta['date'] )->format('d-m-Y')   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachAssessmentTimeDta['type']   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachAssessmentTimeDta['cat_id']   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachAssessmentTimeDta['sub_cat_id']   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachAssessmentTimeDta['desc']   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachAssessmentTimeDta['response_time']   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachAssessmentTimeDta['assessment_time']   }}

                                </td>
                                <td class="table-data">
                                  {{ $breachAssessmentTimeDta['rectification_time']   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachAssessmentTimeDta['breach_response_time']   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachAssessmentTimeDta['breach_assessment_time']   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachAssessmentTimeDta['breach_rectification_time']   }}
                                </td>
                              </tr>
                              @endforeach
                            </table>
                          </div>
                          {{-- <div style="display: flex; justify-content: center; display:none" id="rectification-sla-breach"> --}}
                            <div style="width: 100%; overflow: auto; display:none; height:400px;" id="rectification-sla-breach">
                            <table class="table" border-color="grey">
                              <tr class="heading-row" style="position: sticky; top: 0px;">
                                <th rowspan="2" class="table-data" style="border-radius: 10px 0px 0px 0px;">
                                  WO Number
                                </th>
                                <th rowspan="2" class="table-data">
                                  Date
                                </th>
                                <th rowspan="2" class="table-data">
                                  Service Type
                                </th>
                                <th rowspan="2" class="table-data">
                                  Service Category
                                </th>
                                <th rowspan="2" class="table-data">
                                  Service Sub-category
                                </th>
                                <th rowspan="2" class="table-data">
                                  Issue Description
                                </th>
                                <th rowspan="2" class="table-data">
                                  Response Time<br>
                                  (HH:MM)
                                </th>
                                <th rowspan="2" class="table-data">
                                  Assessment Time<br>
                                  (HH:MM)
                                </th>
                                <th rowspan="2" class="table-data">
                                  Rectification Time<br>
                                  (HH:MM)
                                </th>
                                <th colspan="3" class="table-data" style="border-radius: 0px 10px 0px 0px;">
                                  SLA Breach
                                </th>
                              </tr>
                              <tr class="heading-row">
                                <th class="table-data">
                                  Response
                                </th>
                                <th class="table-data">
                                  Assessment
                                </th>
                                <th class="table-data">
                                  Rectification
                                </th>
                              </tr>
                                @php
                                $i = 1;
                                @endphp
                           @foreach($breachRectificationTime as $breachRectificationTimeDta)
                              <tr>
                                <td class="table-data">
                                    {{   $breachRectificationTimeDta['id'] }}
                                </td>
                                <td class="table-data">
                                  {{  Carbon\Carbon::parse( $breachRectificationTimeDta['date'] )->format('d-m-Y')   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachRectificationTimeDta['type']   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachRectificationTimeDta['cat_id']   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachRectificationTimeDta['sub_cat_id']   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachRectificationTimeDta['desc']   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachRectificationTimeDta['response_time']   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachRectificationTimeDta['assessment_time']   }}

                                </td>
                                <td class="table-data">
                                  {{ $breachRectificationTimeDta['rectification_time']   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachRectificationTimeDta['breach_response_time']   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachRectificationTimeDta['breach_assessment_time']   }}
                                </td>
                                <td class="table-data">
                                  {{ $breachRectificationTimeDta['breach_rectification_time']   }}
                                </td>
                              </tr>
                              @endforeach
                            </table>
                          </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('custom-scripts')
<script>
       function gettabel(type){
        var type = type
        if(type == 'table-1'){
            $('#work-order-open').show();
            $('#work-order-closed').hide();
            $('#response-sla-breach').hide();
            $('#assessment-sla-breach').hide();
            $('#rectification-sla-breach').hide();
        }
        if(type == 'table-2'){
            $('#work-order-open').hide();
            $('#work-order-closed').show();
            $('#response-sla-breach').hide();
            $('#assessment-sla-breach').hide();
            $('#rectification-sla-breach').hide();
        }
        if(type == 'table-3'){
            $('#work-order-open').hide();
            $('#work-order-closed').hide();
            $('#response-sla-breach').show();
            $('#assessment-sla-breach').hide();
            $('#rectification-sla-breach').hide();
        }
        if(type == 'table-4'){
            $('#work-order-open').hide();
            $('#work-order-closed').hide();
            $('#response-sla-breach').hide();
            $('#assessment-sla-breach').show();
            $('#rectification-sla-breach').hide();
        }
        if(type == 'table-5'){
            $('#work-order-open').hide();
            $('#work-order-closed').hide();
            $('#response-sla-breach').hide();
            $('#assessment-sla-breach').hide();
            $('#rectification-sla-breach').show();
        }

       }
</script>
@endpush

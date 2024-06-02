@php

    $sorter     = isset($_GET['sort']) ? explode('.', $_GET['sort'])[0] : 'id';
    $direction  = isset($_GET['sort']) ? explode('.', $_GET['sort'])[1] : 'asc';
    $dirIcon    = $direction == 'asc' ? 'desc' : 'asc';
@endphp
<style>

                          .tab-sla{
                            display: flex;
                            align-items: center;
                              justify-content: start;
                              padding: 10px;
                              height: 35px;
                              border-radius: 5px;
                              width: 17%;
                              box-shadow: 2px 2px 2px 2px #888888;
                              /* font-size: 12px; */
                              color: #3C4E5E;
                          }
                          .tab-sla-nmbr{
                            margin-left: 10px;
                            padding-right: 10px;
                            border-right: 1px solid #D3D3D3
                          }

</style>
@extends('company.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/company/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>{{ __('language.Sub Companies') }}</li>
        <li class="active">{{isset($status) ? $status : 'Search'}}</li>
    </ul>



    <!-- END BREADCRUMB -->
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
            @include('company.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">
                    <form class="form-horizontal" method="get" action="/company/sla/filter-order-dashboard">
                        @include('company.sla.search')
                    </form>
                    <div class="panel-body">
                        <div style="display: flex; padding: 10px 10px ; margin-bottom: 20px; align-items: center; justify-content: center; gap: 20px;">
                            <div class="tab-sla"  onclick="sayHello();" style="cursor: pointer;">
                              <div class="tab-sla-nmbr">{{  count($workOrderOpen) }}</div>
                              <div style="padding-left: 3px;" onclick="gettabel('table-1');">Work Order Open</div>
                            </div>
                            <div class="tab-sla" style="cursor: pointer;"><div class="tab-sla-nmbr">{{  count($workOrderClosed) }}</div>
                              <div style="padding-left: 3px;" onclick="gettabel('table-2');">Work Order Closed</div></div>
                            <div class="tab-sla" style="cursor: pointer;"><div class="tab-sla-nmbr">{{  count($breachResponseTime) }}</div>
                              <div style="padding-left: 3px;" onclick="gettabel('table-3');">Response SLA Breach</div></div>
                            <div class="tab-sla" style="cursor: pointer;"><div class="tab-sla-nmbr">{{  count($breachAssessmentTime) }}</div>
                              <div style="padding-left: 10px;" onclick="gettabel('table-4');">Assessment SLA Breach</div></div>
                              <div class="tab-sla" style="cursor: pointer;"><div class="tab-sla-nmbr">{{  count($breachRectificationTime) }}</div>
                              <div style="padding-left: 10px;" onclick="gettabel('table-5');">Rectification SLA Breach</div></div>
                          </div>
                        <div class="table-responsive">
                            <table class="table" style="    font-size: 12px !important; white-space: nowrap;!important">
                                <thead>
                                <tr>
                                    <td><a href="?sort=id.{{$sorter == 'id' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'id' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> ID </a></td>
                                    <td><a href="?sort=date.{{$sorter == 'date' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'date' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> Date</a></td>
                                    <td><a href="?sort=type.{{$sorter == 'type' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'type' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> Services Type </a></td>
                                    <td><a href="?sort=cat_id.{{$sorter == 'cat_id' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'cat_id' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> Services Category </a></td>
                                    <td><a href="?sort=sub_cat_id.{{$sorter == 'sub_cat_id' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'sub_cat_id' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> Services Sub Category</a></td>
                                    <td><a href="?sort=response_time.{{$sorter == 'response_time' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'response_time' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> Response Time (HH:MM)</a></td>
                                    <td><a href="?sort=assessment_time.{{$sorter == 'assessment_time' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'assessment_time' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> Assessment Time (HH:MM)</a></td>
                                    <td><a href="?sort=rectification_time.{{$sorter == 'rectification_time' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'rectification_time' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> Rectification Time (HH:MM)</a></td>
                                    <td><a href="?sort=breach_response_time.{{$sorter == 'breach_response_time' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'breach_response_time' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> Breach Response Time</a></td>
                                    <td><a href="?sort=breach_assessment_time.{{$sorter == 'breach_assessment_time' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'breach_assessment_time' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> Breach Assessment Time</a></td>
                                    <td><a href="?sort=breach_rectification_time.{{$sorter == 'breach_rectification_time' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'breach_rectification_time' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> Breach Rectification Time</a></td>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($workOrderOpen as $dtworkOrderOpen)
                                    <tr>
                                        <td>{{$dtworkOrderOpen['id']}}</td>
                                        <td>{{ \Carbon\Carbon::parse($dtworkOrderOpen['date'])->format('d-m-Y')}}</td>
                                        <td>{{$dtworkOrderOpen['type']}}</td>
                                        <td>
                                            {{$dtworkOrderOpen['cat_id']}}
                                        </td>
                                        <td>{{ $dtworkOrderOpen['sub_cat_id'] }}</td>
                                        {{-- <td>{{ $dtworkOrderOpen['desc'] }}</td> --}}
                                        <td>{{ $dtworkOrderOpen['response_time'] }}</td>
                                        <td>{{ $dtworkOrderOpen['assessment_time'] }}</td>
                                        <td>{{ $dtworkOrderOpen['rectification_time'] }}</td>
                                        <td>{{ $dtworkOrderOpen['breach_response_time'] }}</td>
                                        <td>{{ $dtworkOrderOpen['breach_assessment_time'] }}</td>
                                        <td>{{ $dtworkOrderOpen['breach_rectification_time'] }}</td>
                                    </tr>

                                    <!-- danger with sound -->

                                    <!-- end danger with sound -->

                                    <!-- danger with sound -->

                                    <!-- end danger with sound -->

                                @endforeach
                                </tbody>
                            </table>
                            {{ $workOrderOpen->appends(request()->input())->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
    $('#main_cats').on('change', function (e) {
            var parent_id = $('#main_cats').val();
            if (parent_id) {
                $.ajax({
                    url: '/company/get_sub_cat/'+parent_id,
                    type: "GET",
                    dataType: "json",

                    success: function (data) {
                        $('#sub_cats').empty();
                        $('#sub_cats').append('<option selected disabled> Select a Sub Category </option>');
                        $.each(data, function (i, sub_cat) {
                            debugger;
                            var selected = [{{  request('sub_cats') }}]
                            var includes = selected

                            if(includes == true)
                                $('#sub_cats').append('<option value="' + sub_cat.id + '" selected>' + sub_cat.en_name + '</option>');
                            else
                                $('#sub_cats').append('<option value="' + sub_cat.en_name + '">' + sub_cat.en_name + '</option>');
                        });
                    }
                });

            }
        });
</script>
@endsection

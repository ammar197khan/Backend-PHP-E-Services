@extends('provider.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/provider/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li><a href="/provider/collaborations">{{ __('language.Collaborations') }}</a></li>
        <li class="active">Update Services Fees</li>
    </ul>
    <!-- END BREADCRUMB -->
    @include('provider.layouts.message')
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
                <form class="form-horizontal" method="post" action="/provider/collaboration/services/fees/update" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                Update Services Fees
                            </h3>
                        </div>
                        <div class="panel-body">
                            <span align="center" style="background-color: #1d75b3; color: white; padding: 1px; font-size: 12px">First input Urgent, Second input Scheduled, Third input Emergency</span>
                            <div class="row">
                                @foreach($categories as $parentName => $subscriptions)
                                    <div class="col-md-12" >
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <div class="row">
                                                    <label class="switch" >
                                                        <h3 class="panel-title" style="float: left;">{{$parentName}}</h3>
                                                        <input type="hidden" value="{{$company_id}}" name="company_id" />
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="panel-body">
                                                <ul class="list-group border-bottom">
                                                    @foreach($subscriptions as $category)
                                                        <div class="col-md-6" style="margin-bottom: 2px !important;">
                                                            <div class="col-md-6" style="margin-bottom: 3px; margin-top: 11px;">
                                                                <li class="list-group-item">
                                                                    {{$category->en_name}}
                                                                </li>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="col-md-4">
                                                                    <label>Urgent</label>
                                                                  <input value="{{ !empty($category->urgent_fee) ? number_format((float)$category->urgent_fee, 2, '.', '') : 0.00 }}" class="form-control" type="number" name="urgent_fees[{{$category->id}}]" style="width: 80px;" required>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label>Scheduled</label>
                                                                    <input value="{{ !empty($category->scheduled_fee) ? number_format((float)$category->scheduled_fee, 2, '.', '') : 0.00  }}" class="form-control" type="number" name="scheduled_fees[{{$category->id}}]" style="width: 80px;" required>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label>Emergency</label>
                                                                    <input value="{{ !empty($category->emergency_fee) ? number_format((float)$category->emergency_fee, 2, '.', '') : 0.00 }}" class="form-control" type="number" name="emergency_fees[{{$category->id}}]" style="width: 80px;" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>

                            <div class="panel-footer">
                                <button type="reset" class="btn btn-default">{{ __('language.Reset') }}</button> &nbsp;
                                <button class="btn btn-primary pull-right">
                                    Update
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

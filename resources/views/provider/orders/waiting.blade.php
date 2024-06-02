@extends('provider.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/provider/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>{{ __('language.Orders') }}</li>
        <li class="active">waiting</li>
    </ul>
    <!-- END BREADCRUMB -->

    <style>
        .image
        {
            height: 50px;
            width: 50px;
            border: 1px solid #29B2E1;
            border-radius: 100px;
            box-shadow: 2px 2px 2px darkcyan;
        }
    </style>
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
            @include('provider.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <a href="/provider/orders/open/waiting/upload/view">
                          <button type="button" class="btn btn-info">
                            <i class="fa fa-upload"></i>
                            Upload Excel Tech Details & Items
                          </button>
                        </a>
                        {{--<a href="/provider/orders/images/view"><button type="button" class="btn btn-info"> Upload images compressed file </button></a>--}}

                        <div style="float: right; margin-right: 10px">
                            <select name="provider_status" class="form-control" onchange="location = this.value;">
                                <option @if(request('provider_status') == 'urgent') selected @endif value="/provider/orders/urgent?provider_status=urgent">Urgent</option>
                                <option @if(request('provider_status') == 'scheduled') selected @endif value="/provider/orders/scheduled?provider_status=scheduled">Scheduled</option>
                                <option @if(request('provider_status') == 're_scheduled') selected @endif value="/provider/orders/re_scheduled?provider_status=re_scheduled">Re-Scheduled</option>
                                <option @if(request('provider_status') == 'canceled') selected @endif value="/provider/orders/canceled?provider_status=canceled">Canceled</option>
                                <option @if(request('provider_status') == 'waiting') selected @endif value="/provider/orders/open/waiting?provider_status=waiting">Waiting</option>
                            </select>
                        </div>

                    </div>


                    <div class="panel-body">
                        @foreach($shows as $show)
                            @if(strpos($show, \Carbon\Carbon::now()->format('Y-m-d')))
                            <h2>New export file</h2>
                            <a href="{{$show}}" style="padding: 15px;"><button type="button" class="btn btn-success" style="font-size: 18px"> export <i style="font-size: 14px" class="fa fa-file-excel-o"></i></button></a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

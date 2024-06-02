@extends('company.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/company/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li class="active">{{ __('language.Collaborations') }}</li>
    </ul>
    <!-- END BREADCRUMB -->

    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
            @include('company.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">
                    {{--<form class="form-horizontal" method="get" action="/company/collaborations/search">--}}
                        {{--<div class="form-group">--}}
                            {{--<div class="col-md-6 col-xs-12">--}}
                                {{--<div class="input-group" style="margin-top: 10px;">--}}
                                    {{--<input type="text" class="form-control" name="search" value="{{isset($search) ? $search : ''}}" placeholder="Search by name or email" style="margin-top: 1px;"/>--}}
                                    {{--<span class="input-group-addon btn btn-default">--}}
                                            {{--<button class="btn btn-default">Search now</button>--}}
                                    {{--</span>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</form>--}}
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>#{{ __('language.ID') }}</th>
                                    <th>{{ __('language.Company Name') }}</th>
                                    <th>{{ __('language.Date') }}</th>
                                    <th>{{ __('language.Orders') }}</th>
                                    <th>{{ __('language.logo') }}</th>
{{--                                    @if(company()->hasPermissionTo('statistics_general'))--}}
                                        <th>{{ __('language.Operations') }}</th>
{{--                                    @endif--}}
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($providers as $provider)
                                    <tr>
                                        <td>{{$provider->provider->id}}</td>
                                        <td>{{$provider->provider->en_name}}</td>
                                        <td>{{$provider->provider->created_at->toDateString()}}</td>
                                        <td>{{$provider->orders}}</td>
                                        <td>
                                            <img src="/providers/logos/{{$provider->provider->logo}}" class="image_radius"/>
                                        </td>
{{--                                        @if(company()->hasPermissionTo('statistics_general'))--}}
                                            <td>

                                                @if(company()->hasPermissionTo('Statistics collaboration'))
                                                    <a title="View Statistics" href="/company/collaboration/{{$provider->id}}/statistics"><button class="btn btn-info btn-condensed"><i class="fa fa-area-chart"></i></button></a>
                                                @endif
                                                @if(company()->hasPermissionTo('Order sheet collaboration'))
                                                    <a title="Orders Info Sheet" href="/company/collaboration/{{$provider->id}}/orders/request"><button class="btn btn-success btn-condensed"><i class="fa fa-mail-reply"></i></button></a>
                                                @endif
                                                @if(company()->hasPermissionTo('Fees sheet collaboration'))
                                                    <a title="Fees Info Sheet" href="/company/collaboration/{{$provider->provider->id}}/fees/show"><button class="btn btn-success btn-condensed"><i class="fa fa-table"></i></button></a>
                                                @endif
                                            </td>
{{--                                        @endif--}}
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{$providers->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

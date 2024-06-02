@extends('company.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    @php
        if($company->active == 1)
        {
            $state = 'active';
            $name = 'Active';
        }
        elseif($company->active == 0)
        {
            $state = 'suspended';
            $name = 'Suspended';
        }
    @endphp

    <ul class="breadcrumb">
        <li><a href="/company/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li class="active">{{$company->en_name}}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- PAGE TITLE -->
    <div class="page-title">
        <h2><span class="fa fa-eye"></span> {{ __('language.View Company Info') }}</h2>
        @if(company()->hasPermissionTo('Edit company info'))
        <a title="{{ __('language.View') }}" href="{{route('company.profile.info')}}" style="float:right; padding-right: 10px">
            <button class="btn btn-success btn-condensed"><i class="fa fa-edit"></i></button></a>
        @endif
    </div>
    <!-- END PAGE TITLE -->
    @include('company.layouts.message')
    <!-- PAGE CONTENT WRAPPER -->
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-3 col-sm-4 col-xs-5">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h3><span class="fa fa-industry"></span> {{$company->en_name}} </h3>
                            <p>
                                @if($company->active == 1)
                                    <span class="label label-success label-form"> {{ __('language.Active company') }} </span>
                                @elseif($company->active == 0)
                                    <span class="label label-primary label-form"> {{ __('language.Suspended company') }} </span>
                                @endif
                            </p>
                                 <div class="text-center" id="user_image">
                                <img src="/companies/logos/{{$company->logo}}" class="img-thumbnail" width="300px" height="300px"/>
                            </div>
                        </div>
                        <div class="panel-body form-group-separated">
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label"># {{ __('language.ID') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$company->id}} </span>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="col-md-6 col-sm-8 col-xs-7">
                    <div class="panel panel-default form-horizontal">
                        <div class="panel-body">
                            <h3><span class="fa fa-pencil"></span>{{ __('language.Profile') }}</h3>
                        </div>
                        <div class="panel-body form-group-separated">
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Location') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$company->address->parent->en_name}} - {{$company->address->en_name}} </span>
                                </div>

                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.English Name') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <label class="form-control">{{$company->en_name}}</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Arabic Name') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <label class="form-control">{{$company->ar_name}}</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.English Description') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <textarea disabled class="form-control" name="en_desc" rows="5" style="color: black">
                                        {{$company->en_desc}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Arabic Description') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <textarea disabled class="form-control" name="ar_desc" rows="5" style="color: black">
                                        {{$company->ar_desc}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Email') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <label class="form-control">{{$company->email}}"</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Phone') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <div id="field">
                                        @foreach(unserialize($company->phones) as $phone)
                                            <label class="form-control">{{$phone}}</label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>

            <div class="col-md-3">
                <div class="panel panel-default form-horizontal">
                    <div class="panel-body">
                        <h3><span class="fa fa-info-circle"></span> {{ __('language.Quick Info') }}</h3>
                    </div>
                    <div class="panel-body form-group-separated">
                        <div class="form-group">
                            <label class="col-md-4 col-xs-5 control-label">{{ __('language.Registration') }}</label>
                            <div class="col-md-8 col-xs-7 line-height-30">{{$company->created_at}}</div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 col-xs-5 control-label line-height-30">{{ __('language.Arabic Description') }}</label>
                            <div class="col-md-8 col-xs-7 line-height-30"><a href="users/active">{{$company->users->count()}}</a></div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 col-xs-5 control-label line-height-30">{{ __('language.Orders') }}</label>
                            <div class="col-md-8 col-xs-7 line-height-30">
                                @if(isset($company->orders))
                                    <a href="/company/orders/all">{{ $company->orders->count() }} </a>
                                @else
                                    <span class="label label-default">{{ __('language.No updates yet') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-3">


                <div class="panel panel-default form-horizontal">
                    <div class="panel-body">
                        <h3><span class="fa fa-info-circle"></span> Order Process</h3>
                    </div>
                    <div class="panel-body form-group-separated">
                        <div class="form-group">
                            <label class="col-md-9 col-xs-7 control-label" style="text-align:left">Supervisor Assessment</label>

                            <div class="col-md-3 col-xs-5">
                                <input type="radio" class="form-check-input" name="order_process_id" value="1"  @if(isset($company) && isset( $company->orderProcessType) &&  isset($company->orderProcessType->name) && ($company->orderProcessType->name == 'Supervisor Assessment'))  checked
                                @endif  >
                            </div>

                        </div>
                         <div class="form-group">
                        <label class="col-md-9 col-xs-7 control-label" style="text-align:left">Direct Technician Assignment</label>
                                <div class="col-md-3 col-xs-5">
                                    <input type="radio" class="form-check-input" name="order_process_id" value = "2"   @if( isset($company) && isset( $company->orderProcessType) &&  isset($company->orderProcessType->name) && ($company->orderProcessType->name == 'Direct Technician Assignment'))  checked
                                    @endif
                                    >
                                </div>

                            </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- END PAGE CONTENT WRAPPER -->
@endsection

@extends('company.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/company/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li> <a href="/company/house_types">{{ __('language.House Types') }}</a></li>
        <li class="active">
            {{isset($house_type) ? __('language.Update a house type') :  __('language.Create a house type') }}
        </li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- END BREADCRUMB -->

    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
                <form class="form-horizontal" method="post" action="{{isset($house_type) ? route('company.house_types.update',$house_type->id) : route('company.house_types.store')}}">
                    {{csrf_field()}}
                    @if( isset($house_type) )
                        {{method_field('PATCH')}}
                    @endif
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                {{isset($house_type) ? __('language.Update a house type') : __('language.Create a house type')}}
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group {{ $errors->has('ar_name') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Arabic Name') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="ar_name" @if(isset($house_type)) value="{{$house_type->ar_name}}" @else {{old('ar_name')}} @endif/>
                                        <span class="input-group-addon"><span class="fa fa-cube"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'ar_name'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('en_name') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.English Name') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="en_name" @if(isset($house_type)) value="{{$house_type->en_name}}" @else {{old('en_name')}} @endif/>
                                        <span class="input-group-addon"><span class="fa fa-cube"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'en_name'])
                                </div>
                            </div>

                            <input type="hidden" name="company_id" value="{{company()->company_id}}">
                            @if(isset($house_type))
                                <input type="hidden" name="house_type_id" value="{{$house_type->id}}">
                            @endif
                        </div>

                        <div class="panel-footer">
                            <button type="reset" class="btn btn-default">{{ __('language.Reset') }}</button> &nbsp;
                            <button class="btn btn-primary pull-right">
                                {{isset($house_type) ? __('language.Update') : __('language.Create') }}
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>

@endsection

@extends('company.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/company/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li><a href="/company/orders/urgent">{{ __('language.Orders') }}</a></li>
        <li class="active">{{ __('language.Upload technicians excel file') }}</li>
    </ul>
    <!-- END BREADCRUMB -->

    <div class="page-content-wrap">

        <div class="row">
            <div class="col-md-12">
                @include('company.layouts.message')
                <form class="form-horizontal" method="post" @if(Request::is('company/orders/urgent/excel/view'))
                action="/company/orders/urgent/excel/upload" @else action="/company/orders/scheduled/excel/upload" @endif enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                {{ __('language.Upload File') }}
                            </h3>
                        </div>
                        <div class="panel-body">
                            <input type="hidden" name="type" value="{{$type}}">
                            <div class="form-group {{ $errors->has('file') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.File') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="file" class="fileinput btn-info" name="file" id="cp_photo" data-filename-placement="inside" title="{{ __('language.Select Excel File') }}"/>
                                        @include('admin.layouts.error', ['input' => 'id'])
                                        @include('admin.layouts.error', ['input' => 'smo'])
                                        @include('admin.layouts.error', ['input' => 'provider_id'])
                                        @include('admin.layouts.error', ['input' => 'service_type'])
                                        @include('admin.layouts.error', ['input' => 'cat_id'])
                                        @include('admin.layouts.error', ['input' => 'tech_id'])
                                        @include('admin.layouts.error', ['input' => 'user_id'])
                                        @include('admin.layouts.error', ['input' => 'place'])
                                        @include('admin.layouts.error', ['input' => 'part'])
                                        @include('admin.layouts.error', ['input' => 'desc'])
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="panel-footer">
                            <button type="reset" class="btn btn-default">{{ __('language.Reset') }}</button> &nbsp;
                            <button class="btn btn-primary pull-right">
                                 {{ __('language.Upload') }}
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>


@endsection

@extends('provider.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/provider/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>{{ __('language.Warehouse') }}</li>
        <li class="active">{{ __('language.Upload warehouse excel file') }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
                @include('provider.layouts.message')

                <form class="form-horizontal" method="post" action="/provider/orders/excel/tech/upload" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                               Upload File
                            </h3>
                        </div>
                        <div class="panel-body">

                            <div class="form-group {{ $errors->has('file') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">File</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="file" class="fileinput btn-info" name="file" id="cp_photo" data-filename-placement="inside" title="Select Excel File"/>
                                    </div>
                                    @include('provider.layouts.error', ['input' => 'file'])
                                    @include('provider.layouts.error', ['input' => 'order_id'])
                                    @include('provider.layouts.error', ['input' => 'item_id'])
                                    @include('provider.layouts.error', ['input' => 'taken'])
                                    @include('provider.layouts.error', ['input' => 'working_hours'])
                                    @include('provider.layouts.error', ['input' => 'desc'])
                                </div>
                            </div>

                        </div>

                        <div class="panel-footer">
                            <button type="reset" class="btn btn-default">{{ __('language.Reset') }}</button> &nbsp;
                            <button class="btn btn-primary pull-right">
                               Upload
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>


@endsection

@extends('provider.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/provider/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li><a href="/provider/technicians/active">{{ __('language.Technicians') }}</a></li>
        <li class="active">Upload technicians images compressed file</li>
    </ul>
    <!-- END BREADCRUMB -->
    {{--{{dd($errors)}}--}}
    <div class="page-content-wrap">

        <div class="row">
            <div class="col-md-12">
                @include('provider.layouts.message')
                <form class="form-horizontal" method="post" action="/provider/technician/images/upload" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                               Upload Compressed File
                            </h3>
                        </div>
                        <div class="panel-body">

                            <div class="form-group {{ $errors->has('file') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">File</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="file" class="fileinput btn-info" name="file" id="cp_photo" data-filename-placement="inside" title="Select Zip"/>
                                    </div>
                                    <br/>
                                    <span class="label label-primary" style="font-size: 10px;">{{ __('language.Technicians & Images with the same name as the Badge ID will be assigned together') }}</span><br/><br/>
                                    <span class="label label-warning" style="font-size: 10px;">Images names must not contain any dots</span>
                                    <span class="label label-success" style="font-size: 10px;">Example : A1554.jpg</span>
                                    @include('admin.layouts.error', ['input' => 'file'])
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

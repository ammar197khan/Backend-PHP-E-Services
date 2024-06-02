@extends('company.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/company/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li><a href="/company/users/active">{{ __('language.Users') }}</a></li>
        <li class="active">{{ __('language.Upload users excel file') }}</li>
    </ul>
    <!-- END BREADCRUMB -->

    <div class="page-content-wrap">

        <div class="row">
            <div class="col-md-12">
                @include('company.layouts.message')

                @if (Session::has('failures'))
                    <div class="alert alert-danger" role="alert">
                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                        {{ __('language.All rows inserted except') }}:
                        @foreach (array_unique(session('rows')) as $row)
                          <span class="label label-default">{{$row}}</span>
                        @endforeach
                        <br>
                    </div>
                    <div class="alert alert-danger" role="alert">
                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">{{ __('language.Closet') }}</span></button>
                        @foreach (session('failures') as $failure)
                          <li>{{ $failure[0] }}</li>
                        @endforeach
                    </div>
                @endif

                <form class="form-horizontal" method="post" action="/company/user/excel/upload" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                {{ __('language.Upload File') }}
                            </h3>
                        </div>
                        <div class="panel-body">

                            <div class="form-group {{ $errors->has('file') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.File') }}</label>
                                <div class="col-md-1 col-xs-12">
                                    <div class="input-group">
                                        <input type="file" accept=".xlsx,.csv,.tsv,.ods,.xls,.slk,.xml" class="fileinput btn-info" name="file" id="cp_photo" data-filename-placement="inside" title="Select Excel File" required/>
                                    </div>
                                </div>
                                <div class="col-md-3 control-label">
                                  <a href="{{url('companies\Users_Example.xlsx')}}">
                                      <i class="fa fa-info-circle" aria-hidden="true"></i>
                                      
                                      {{ __('language.Example File') }}
                                  </a>
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

@extends('admin.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>{{ __('language.Company') }}</li>
        <li>{{ __('language.Users') }}</li>
    </ul>
    <!-- END BREADCRUMB -->

    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
            @include('admin.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">

{{--                    <div class="panel-heading">--}}
{{--                        <a href="/company/users/active/excel/export" style="float: right;"><button type="button" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Export Users</button></a>--}}
{{--                    </div>--}}

                    <form class="form-horizontal" method="get" action="/admin/company/{{$id}}/users/search">
                        <div class="form-group">
                            <div class="col-md-6 col-xs-12">
                                <div class="input-group" style="margin-top: 10px;">
                                    <input type="text" class="form-control" name="search" value="{{isset($search) ? $search : ''}}" placeholder="{{ __('language.Search by user badge_id,name,email or phone') }}" style="margin-top: 1px;"/>
                                    <span class="input-group-addon btn btn-default">
                                            <button class="btn btn-default">{{ __('language.Search now') }}</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ __('language.Badge ID') }}</th>
                                    <th> {{ __('language.Sub Company') }}</th>
                                    <th>{{ __('language.English Name') }}</th>
                                    <th>{{ __('language.Arabic Name') }}</th>
                                    <th>{{ __('language.Email') }}</th>
                                    <th>{{ __('language.Phone') }}</th>
                                    <th>{{ __('language.Image') }}</th>
                                    <th>{{ __('language.Operations') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{$user->badge_id}}</td>
                                        <td>{{$user->sub_company->en_name}}</td>
                                        <td>{{$user->en_name}}</td>
                                        <td>{{$user->ar_name}}</td>
                                        <td>{{$user->email}}</td>
                                        <td>{{$user->phone}}</td>
                                        <td>
                                            <img src="/companies/users/{{$user->image}}" class="image_radius"/>
                                        </td>

                                        <td>
                                            <a title="{{ __('language.View User')}}" href="/admin/company/user/{{$user->id}}/view"><button class="btn btn-info btn-condensed"><i class="fa fa-eye"></i></button></a>
                                        </td>

                                    </tr>

                                @endforeach

                                </tbody>
                            </table>
                            {{$users->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready( function() {
            $('#table').dataTable( {
                "iDisplayLength": 50
            } );
        } )
    </script>
@endsection

@extends('admin.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">Home</a></li>
        <li>{{ __('language.Application Settings') }}</li>
        <li class="active">Complain and Suggestions</li>
    </ul>
    <!-- END BREADCRUMB -->
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12  col-xs-12">
            @include('admin.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">
                    <div class="panel-body" style="overflow: auto;">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="rtl_th">{{ __('language.Title') }}</th>
                                    <th class="rtl_th">{{ __('language.Description') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($complains as $complain)
                                <tr>
                                    <td>
                                        {!! $complain->title->en_title !!}
                                    </td>
                                    <td>
                                        {!! $complain->desc !!}
                                    </td>
                                </tr>
                                @endforeach

                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

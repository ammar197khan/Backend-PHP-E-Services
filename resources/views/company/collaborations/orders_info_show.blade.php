@extends('company.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/company/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li><a href="/company/collaborations">{{ __('language.Collaborations') }}</a></li>
        <li>{{ __('language.Orders Info Sheet') }}</li>
        <li class="active">{{ __('language.Show') }}</li>
    </ul>
    <!-- END BREADCRUMB -->

    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
            @include('company.layouts.message')
                    <!-- START DATATABLE EXPORT -->
                    <div class="panel panel-default">
                        <div class="panel-heading">

                            <!-- PAGE TITLE -->
                            <div class="btn-group pull-left">
                                <h2><span class="fa fa-industry"> {{$provider->name}}</span></h2>
                                <h2><span class="fa fa-calendar"> {{ __('language.From') }} {{$from}} {{ __('language.To') }} {{$to}} </span></h2>

                            </div>
                            <!-- END PAGE TITLE -->

                            <div class="btn-group pull-right">
                                <form method="post" action="/company/collaboration/orders/invoice/export">
                                    {{csrf_field()}}
                                    <input type="hidden" name="coll_id" value="{{$coll_id}}">
                                    <input type="hidden" name="from" value="{{$from}}">
                                    <input type="hidden" name="to" value="{{$to}}">
                                    <button type="submit" class="btn btn-success"> {{ __('language.Export Orders Invoice') }} <i class="fa fa-file-excel-o"></i> </button>
                                </form>
                            </div>

                        </div>

                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ __('language.Category') }}</th>
                                    <th>{{ __('language.Urgent') }}</th>
                                    <th>{{ __('language.Scheduled') }}</th>
                                    <th>{{ __('language.Re-Scheduled') }}</th>
                                    <th>{{ __('language.QTY') }}</th>
                                    <th>Rates</th>
                                    <th class="sorting_asc" aria-sort="ascending">{{ __('language.Total') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($cats as $cat)
                                    <tr>
                                        <td>{{isset($cat->name) ? $cat->name : ''}}</td>
                                        <td>{{isset($cat->urgent) ? $cat->urgent : ''}}</td>
                                        <td>{{isset($cat->scheduled) ? $cat->scheduled : ''}}</td>
                                        <td>{{isset($cat->re_scheduled) ? $cat->re_scheduled : ''}}</td>
                                        <td>{{isset($cat->quantity) ? $cat->quantity : ''}}</td>
                                        <td>{{isset($cat->rates) ? $cat->rates : ''}}</td>
                                        <td>{{isset($cat['total']) ? $cat['total'] : ''}}</td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>

            </div>
        </div>
    </div>
@endsection

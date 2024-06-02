@extends('provider.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/provider/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li class="active">{{ __('language.Warehouse') }}</li>
    </ul>
    <!-- END BREADCRUMB -->

    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
            @include('provider.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        @if(provider()->hasPermissionTo('Add item warehouse'))
                            <a href="/provider/warehouse/item/create"><button type="button" class="btn btn-info"> {{ __('language.Add a new item') }} </button></a>
                        @endif
                        @if(provider()->hasPermissionTo('Upload excel warehouse'))
                            <a href="/provider/warehouse/excel/view"><button type="button" class="btn btn-info"> {{ __('language.Upload excel file') }} </button></a>
                        @endif
                        @if(provider()->hasPermissionTo('Upload images warehouse'))
                            <a href="/provider/warehouse/images/view"><button type="button" class="btn btn-info"> {{ __('language.Upload images compressed file') }} </button></a>
                        @endif

                        @if(provider()->hasPermissionTo('Export categories warehouse'))
                            <a href="/provider/warehouse/excel/parts/export" style="float: right;"><button type="button" class="btn btn-success"> Export Parts <i class="fa fa-file-excel-o"></i></button></a>
                        @endif
                        @if(provider()->hasPermissionTo('Export parts warehouse'))
                            <a href="/provider/warehouse/excel/categories/export" style="float: right; margin-right: 3px;"><button type="button" class="btn btn-success"> {{ __('language.Export categories') }} <i class="fa fa-file-excel-o"></i> </button></a>
                        @endif
                    </div>

                    <form class="form-horizontal" method="get" action="/provider/warehouse/search">
                        <div class="form-group">
                            <div class="col-md-6 col-xs-12">
                                <div class="input-group" style="margin-top: 10px;">
                                    <input type="text" class="form-control" name="search" value="{{isset($search) ? $search : ''}}" placeholder="Search by user name,email or phone" style="margin-top: 1px;"/>
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
                                    <th>#</th>
                                    <th>{{ __('language.Status') }}</th>
                                    <th>{{ __('language.code') }}</th>
                                    <th>{{ __('language.English Name') }}</th>
                                    <th>{{ __('language.Arabic Name') }}</th>
                                    <th>{{ __('language.Count') }}</th>
                                    <th>{{ __('language.Image') }}</th>
                                    <th>{{ __('language.Operations') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td>{{$item->id}}</td>
                                        <td>@if($item->active == 1) <span class="label label-success">{{ __('language.Active') }}</span> @else <span class="label label-default">{{ __('language.Suspended') }}</span> @endif</td>
                                        <td>{{$item->code}}</td>
                                        <td>{{$item->en_name}}</td>
                                        <td>{{$item->ar_name}}</td>
                                        <td>{{$item->count}}</td>
                                        <td>
                                            <img src="/warehouses/{{$item->image}}" class="image_radius"/>
                                        </td>
                                        <td>
                                            @if(provider()->hasPermissionTo('Edit item warehouse'))
                                                <a title="Edit Item" href="/provider/warehouse/item/{{$item->code}}/edit"><button class="btn btn-warning btn-condensed"><i class="fa fa-edit"></i></button></a>
                                            @endif

                                            @if(provider()->hasPermissionTo('Suspend item warehouse'))
                                                @if($item->active == 0)
                                                <button class="btn btn-success btn-condensed mb-control" data-box="#message-box-active-{{$item->id}}" title="Activate"><i class="fa fa-check-square"></i></button>
                                                @else
                                                    <button class="btn btn-primary btn-condensed mb-control" data-box="#message-box-suspend-{{$item->id}}" title="{{ __('language.Suspend') }}"><i class="fa fa-minus-square"></i></button>
                                                @endif
                                            @endif
                                            {{--<button class="btn btn-danger btn-condensed mb-control" data-box="#message-box-warning-{{$item->id}}" title="Delete"><i class="fa fa-trash-o"></i></button>--}}
                                        </td>
                                    </tr>

                                    <!-- danger with sound -->
                                    <div class="message-box message-box-success animated fadeIn" data-sound="alert/fail" id="message-box-active-{{$item->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                                                <div class="mb-content">
                                                    <p>{{ __('language.Your are about to activate a warehouse item,it will be visible to be selected by technicians') }} .</p>
                                                    <br/>
                                                    <p>{{ __('language.Are you sure?') }}</p>
                                                </div>
                                                <div class="mb-footer buttons">
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">Close</button>
                                                    <form method="post" action="/provider/warehouse/item/change_status" class="buttons">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="item_code" value="{{$item->code}}">
                                                        <button type="submit" class="btn btn-success btn-lg pull-right">Activate</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end danger with sound -->

                                    <!-- danger with sound -->
                                    <div class="message-box message-box-primary animated fadeIn" data-sound="alert/fail" id="message-box-suspend-{{$item->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                                                <div class="mb-content">
                                                    <p>{{ __("language.Your are about to suspend a warehouse item,and it won't be able visible to be selected by technicians") }} .</p>
                                                    <br/>
                                                    <p>{{ __('language.Are you sure?') }}</p>
                                                </div>
                                                <div class="mb-footer buttons">
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">Close</button>
                                                    <form method="post" action="/provider/warehouse/item/change_status" class="buttons">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="item_code" value="{{$item->code}}">
                                                        <button type="submit" class="btn btn-primary btn-lg pull-right">{{ __('language.Suspend') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end danger with sound -->

                                    <!-- danger with sound -->
                                    {{--<div class="message-box message-box-danger animated fadeIn" data-sound="alert/fail" id="message-box-warning-{{$item->id}}">--}}
                                        {{--<div class="mb-container">--}}
                                            {{--<div class="mb-middle warning-msg alert-msg">--}}
                                                {{--<div class="mb-title"><span class="fa fa-times"></span>Alert !</div>--}}
                                                {{--<div class="mb-content">--}}
                                                    {{--<p>Your are about to delete a warehouse item,and you won't be able to restore its data again .</p>--}}
                                                    {{--<br/>--}}
                                                    {{--<p>Are you sure ?</p>--}}
                                                {{--</div>--}}
                                                {{--<div class="mb-footer buttons">--}}
                                                    {{--<button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">Close</button>--}}
                                                    {{--<form method="post" action="/provider/warehouse/item/delete" class="buttons">--}}
                                                        {{--{{csrf_field()}}--}}
                                                        {{--<input type="hidden" name="item_code" value="{{$item->code}}">--}}
                                                        {{--<button type="submit" class="btn btn-danger btn-lg pull-right">Delete</button>--}}
                                                    {{--</form>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                    <!-- end danger with sound -->
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

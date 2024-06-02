@extends('company.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/company/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li class="active">{{ __('language.Item Requests') }}</li>
    </ul>
    <!-- END BREADCRUMB -->

    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
            @include('provider.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">
                    <form class="form-horizontal" method="get" action="/company/show/item_requests/{{$status}}/search">
                        <div class="form-group">
                            <div class="col-md-6 col-xs-12">
                                <div class="input-group" style="margin-top: 10px;">
                                    <input type="text" class="form-control" name="search" value="{{isset($search) ? $search : ''}}" placeholder="{{ __('language.Search by name,email or phone') }}" style="margin-top: 1px;"/>
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
                                    <th>{{ __('language.User') }}</th>
                                    <th>{{ __('language.Provider') }}</th>
                                    <th> {{ __('language.Item') }}</th>
                                    <th>{{ __('language.Price') }}</th>
                                    <th>{{ __('language.Date') }}</th>
                                    <th>{{ __('language.Image') }}</th>
                                    @if(company()->hasPermissionTo('View details item request') || company()->hasPermissionTo('Approve item request') )
                                        <th>{{ __('language.Operations') }}</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td>{{$item->requested->order_id}}</td>
                                        <td>
                                            @if($item->status == 'awaiting')
                                                <span class="label label-warning">
                                                        {{$item->status}}
                                                    </span>
                                            @elseif($item->status == 'confirmed')
                                                <span class="label label-success">
                                                        {{$item->status}}
                                                    </span>
                                            @else
                                                <span class="label label-danger">
                                                        {{$item->status}}
                                                    </span>
                                            @endif
                                        </td>
                                        <td>{{$item->user_name}}</td>
                                        <td>{{$item->provider}}</td>
                                        <td>{{$item->item_data->en_name}}</td>
                                        <td>{{$item->item_data->price}}</td>
                                        <td>{{$item->created_at}}</td>
                                        <td>
                                            <img src="/warehouses/{{$item->item_data->image}}" class="image_radius"/>
                                        </td>

                                        @if(company()->hasPermissionTo('Approve item request'))
                                            <td>
                                                @if($item->status == 'awaiting')
                                                    <button class="btn btn-success btn-condensed mb-control" data-box="#message-box-success-{{$item->id}}" title="Confirm"><i class="fa fa-check-circle"></i></button>
                                                    <button class="btn btn-danger btn-condensed mb-control" data-box="#message-box-decline-{{$item->id}}" title="Decline"><i class="fa fa-minus-circle"></i></button>
                                                @endif
                                                @if(company()->hasPermissionTo('View details item request'))
                                                    <a title="View" href="/company/order/{{$item->requested->order_id}}/view"><button class="btn btn-info btn-condensed"><i class="fa fa-eye"></i></button></a>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>

                                    <!-- danger with sound -->
                                    <div class="message-box message-box-success animated fadeIn" data-sound="alert/fail" id="message-box-success-{{$item->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                                                <div class="mb-content">
                                                    <p>{{ __('language.Your are about to approve an item request') }}.</p>
                                                    <br/>
                                                    <p>{{ __('language.Are you sure?') }}</p>
                                                </div>
                                                <div class="mb-footer buttons">
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">{{ __('language.Close') }}</button>
                                                    <form method="post" action="/company/change/item_request/change_status" class="buttons">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="state_id" value="{{$item->id}}">
                                                        <input type="hidden" name="status" value="confirmed">
                                                        <button type="submit" class="btn btn-success btn-lg pull-right">{{ __('language.Confirm') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end danger with sound -->

                                    <!-- danger with sound -->
                                    <div class="message-box message-box-danger animated fadeIn" data-sound="alert/fail" id="message-box-decline-{{$item->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                                                <div class="mb-content">
                                                    <p>{{ __('language.Your are about to decline an item request') }}.</p>
                                                    <br/>
                                                    <p>{{ __('language.Are you sure?') }}</p>
                                                </div>
                                                <div class="mb-footer buttons">
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">{{ __('language.Close') }}</button>
                                                    <form method="post" action="/company/change/item_request/change_status" class="buttons">
                                                        {{csrf_field()}}
                                                        <textarea rows="6" name="desc" style="margin-top: 5px;color: black" placeholder="Write reason" required></textarea>
                                                        <input type="hidden" name="state_id" value="{{$item->id}}">
                                                        <input type="hidden" name="status" value="declined">
                                                        <button type="submit" class="btn btn-danger btn-lg pull-right">{{ __('language.Decline') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end danger with sound -->
                                @endforeach

                                </tbody>
                            </table>
                            {{$items->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

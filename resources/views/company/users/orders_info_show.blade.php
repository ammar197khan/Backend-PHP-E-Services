@extends('company.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/company/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li><a href="/company/users/active">{{ __('language.Users') }}</a></li>
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
                                <h2><span class="fa fa-user"> {{$user->name}}</span></h2>
                                @if(isset($from) && isset($to))
                                <h2><span class="fa fa-calendar"> {{ __('language.From') }} {{$from}} {{__('language.To') }} {{$to}} </span></h2>


                            </div>
                            <!-- END PAGE TITLE -->

                            <div class="btn-group pull-right">
                                <form method="post" action="/company/user/orders/invoice/export">
                                    {{csrf_field()}}
                                    <input type="hidden" name="user_id" value="{{$user->id}}">
                                    <input type="hidden" name="from" value="{{$from}}">
                                    <input type="hidden" name="to" value="{{$to}}">
                                    <button type="submit" class="btn btn-success"> {{ __('language.Export Orders Invoice') }} <i class="fa fa-file-excel-o"></i> </button>
                                </form>
                            </div>
                            @endif

                        </div>

                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('language.Category') }}</th>
                                    <th>{{ __('language.Provider') }}</th>
                                    <th>{{ __('language.Date') }}</th>
                                    <th>{{ __('language.Type') }}</th>
                                    <th>{{ __('language.Order Cost') }}</th>
                                    <th>{{ __('language.Items Cost') }}</th>
                                    <th>{{ __('language.Total Cost') }}</th>
                                    <th>{{ __('language.Status') }}</th>
                                    <th>{{ __('language.Operations') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{ isset($order->id) ? $order->id : '' }}</td>
                                        <td>{{isset($order->cat_id) ? !empty($order->category) && !empty($order->category->parent->en_name) ? $order->category->parent->en_name : '' . ' - ' . !empty($order->category)? $order->category->en_name : '' : ''}}</td>
                                        <td>{{isset($order->provider->en_name) ? $order->provider->en_name : '-'}}</td>
                                        <td>{{isset($order->created_at) ? $order->created_at->toDateTimeString() : ''}}</td>
                                        <td>
                                            @if(isset($order->type))
                                                @if($order->type == 'urgent')
                                                    Urgent
                                                @elseif($order->type == 'scheduled')
                                                    {{ __('language.Scheduled') }}
                                                @else
                                                {{ __('language.Re-Schedule') }}
                                                @endif
                                            @endif
                                        </td>
                                        <td>{{isset($order->order_total) ? $order->order_total : ''}}</td>
                                        <td>{{isset($order->item_total) ? $order->item_total : ''}}</td>
                                        <td>{{isset($order->total_amount) ? $order->total_amount : ''}}</td>
                                        <td>
                                            @if($order->completed == 1 && $order->canceled == 0)
                                                <span class="label label-success">Completed</span>
                                            @elseif($order->completed == 0 && $order->canceled == 1)
                                                @if($order->canceled_by == 'user') <span class="label label-danger">{{ __('language.Canceled By User') }}</span>
                                                @elseif($order->canceled_by == 'tech') <span class="label label-danger">{{ __('language.Canceled By Technician') }}</span>
                                                @else <span class="label label-danger">{{ __('language.Canceled By Admin') }}</span>
                                                @endif
                                            @else
                                                <span class="label label-primary">{{ __('language.Open') }}</span>
                                            @endif
                                        </td>
                                        {{-- <td>{{isset($order['total']) ? $order['total'] : ''}}</td> --}}
                                        <td>
                                            @if(isset($order->id))
                                                <a title="View Order" href="/company/order/{{$order->id}}/view"><button class="btn btn-info btn-condensed"><i class="fa fa-eye"></i></button></a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                    <tr class="active">
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td> <b>{{ __('language.Total') }}</b> </td>
                                      <td> <b>{{ $orders->sum('total_amount') }}</b></td>
                                      <td></td>
                                      <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    </div>
@endsection

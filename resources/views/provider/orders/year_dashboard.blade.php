@extends('provider.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/provider/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>{{ __('language.Orders') }}</li>
        <li class="active">{{$type}}</li>
    </ul>
    <!-- END BREADCRUMB -->

    <style>
        .image
        {
            height: 50px;
            width: 50px;
            border: 1px solid #29B2E1;
            border-radius: 100px;
            box-shadow: 2px 2px 2px darkcyan;
        }
    </style>



    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
            @include('provider.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">

                    <form class="form-horizontal" method="get" action="/provider/{{$type}}/search">
                        @include('provider.orders_search')
                    </form>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ __('language.Order No') }}.</th>
                                    <th>{{ __('language.Mso NO.') }}</th>
                                    <th>{{ __('language.Type') }}</th>
                                    <th>{{ __('language.Badge ID') }}</th>
                                    <th>{{ __('language.Technician') }}</th>
                                    <th>{{ __('language.Date') }}</th>
                                    <th>{{ __('language.Status') }}</th>
                                    <th>{{ __('language.Items Approval') }}</th>
                                    <th>{{ __('language.Operations') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{isset($order->id) ? $order->id : '-'}}</td>
                                        <td>{{isset($order->smo) ? $order->smo : '-'}}</td>
                                        <td>{{$order->type}}</td>
                                        <td>@if(isset($order->tech_id))  {{$order->tech->badge_id}} @else {{ __('language.Not selected yet') }} @endif</td>
                                        <td>{{isset($order->tech_id) ? $order->tech->en_name : __('language.Not selected yet') }}</td>
                                        <td>
                                            @if($order->type == 'Urgent')
                                                {{$order->created_at}}
                                            @elseif($order->type == 'Scheduled')
                                                {{$order->scheduled_at}}
                                            @else
                                                {{isset($order->scheduled_at) ? $order->scheduled_at : __('language.Not selected yet') }}
                                            @endif
                                        </td>
                                        <td>@if($order->completed == 1 && $order->canceled == 0) <span class="label label-success">Completed</span> @elseif($order->completed == 0 && $order->canceled == 1) @if($order->canceled_by == 'user') <span class="label label-danger">{{ __('language.Canceled By User') }}</span> @elseif($order->canceled_by == 'tech') <span class="label label-danger">{{ __('language.Canceled By Technician') }}</span> @else <span class="label label-danger">{{ __('language.Canceled By Admin') }}</span> @endif @else <span class="label label-primary">{{ __('language.Open') }}</span> @endif</td>
                                        <td>
                                          @if ($order->isAdminApprovalRequired())
                                            <span class="label label-warning">{{ __('language.Required By Admin') }}</span>
                                          @elseif ($order->isUserApprovalRequired())
                                            <span class="label label-warning">{{ __('language.Required By User') }}</span>
                                          @else
                                            <span class="label label-success">{{ __('language.Not Required') }}</span>
                                          @endif
                                        </td>
                                        <td>
                                            <a title="View" href="/provider/order/{{$order->id}}/view"><button class="btn btn-info btn-condensed"><i class="fa fa-eye"></i></button></a>

                                            {{--<button class="btn btn-danger btn-condensed mb-control" data-box="#message-box-warning-{{$order->id}}" title="Delete"><i class="fa fa-trash-o"></i></button>--}}
                                            @if(!$order->order_expense && $order->canceled != 1)
                                                <a data-toggle="modal" data-target="#modal_update_{{$order->id}}" title="Other expenses" class="buttons"><button class="btn btn-primary btn-condensed"><i class="fa fa-money"></i></button></a>
                                            @endif
                                            @if($order->completed == 0 && $order->canceled == 0)
                                                <a data-toggle="modal" data-target="#modal_finish_order_{{$order->id}}" title="Technician details" class="buttons" onclick="myFunction({{$order}})"><button class="btn btn-dark btn-condensed"><i class="fa fa-edit"></i></button></a>
                                            @endif
                                        </td>

                                        <div class="modal animated fadeIn" id="modal_finish_order_{{$order->id}}" tabindex="-1" role="dialog" aria-labelledby="smallModalHead" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">إغلاق</span></button>
                                                        <h4 class="modal-title" id="smallModalHead">{{ __('language.Technician Details') }}</h4>
                                                    </div>
                                                    <form method="post" action="/provider/order/order_finish" enctype="multipart/form-data">
                                                        {{csrf_field()}}
                                                        <div class="modal-body">

                                                            <div class="col-md-9 mb-3" style="padding-top: 15px">
                                                                <label class="control-label">‫Description </label>
                                                                <textarea class="form-control" rows="5" name="desc" placeholder="Description"></textarea>
                                                            </div>

                                                            <div class="col-md-9 mb-3" style="padding-top: 15px">
                                                                <label class="control-label">{{ __('language.Image Before') }} </label>
                                                                <input type="file" name="before_images[]">
                                                            </div>

                                                            <div class="col-md-9 mb-3" style="padding-top: 15px">
                                                                <label class="control-label">{{ __('language.Image After') }}‬‬ </label>
                                                                <input type="file" name="after_images[]">
                                                            </div>

                                                            @if($order->service_type == 2 || $order->service_type == 3)
                                                                @if(count($order->getThirdCat($order->cat_id)) == 0)
                                                                    <label class="control-label col-md-6" style="margin-top: 15px"><b>There is no technician‬‬ ‫‪jobs for this service</b></label>
                                                                @else
                                                                <div id="show_service_type" class="col-md-12 mb-3" style="padding-top: 15px">
                                                                    <div>
                                                                        <label class="control-label col-md-6"><b>‫‪Technicians‬‬ ‫‪Jobs </b></label>
                                                                        <label class="control-label col-md-6"><b>{{ __('language.Working hours') }}‬‬ </b></label>
                                                                    </div>
                                                                    <div class="col-md-12 mb-3" style="padding-top: 10px">
                                                                    @foreach($order->getThirdCat($order->cat_id) as $third_cats)
                                                                        <label class="control-label col-md-6">{{$third_cats->en_name}} </label>
                                                                        <input type="number" class="col-md-4" name="jobs[{{$third_cats->id}}]" value="" placeholder="0">
                                                                    @endforeach
                                                                    </div>
                                                                </div>
                                                                @endif

                                                                <div class="col-md-12 mb-3" style="padding-top: 15px" id="show_field_items" >
                                                                    <label class="col-md-12">Items‬‬ </label>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label class="col-md-6">Items ID‬‬ </label>
                                                                            <input class="form-control col-md-4" name="item_ids" type="text" placeholder="Choose id items put comma (,) between">
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label class="col-md-6">Taken‬‬ </label>
                                                                            <input class="form-control col-md-4" name="taken" type="text" placeholder="Put taken items comma (,) between">
                                                                        </div>
                                                                    </div>
                                                                    <div id="a_tag">
                                                                        <a target="_blank" href="/provider/warehouse/{{$order->cat_id}}/items">Items</a>
                                                                    </div>
                                                                </div>
                                                            @endif

                                                        </div>

                                                        <input type="hidden" id="modal_order_id" name="order_id" value="{{$order->id}}" />
                                                        <input type="hidden" id="modal_service_type" name="service_type" value="{{$order->service_type}}" />
                                                        <input type="hidden" id="modal_cat_id" name="cat_id" value="{{$order->cat_id}}" />

                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-dark">Save</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </tr>

                                    <!-- danger with sound -->

                                    <div class="message-box message-box-warning animated fadeIn" data-sound="alert/fail" id="message-box-danger-{{$order->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span> {{ __('language.Alert') }} !</div>
                                                <div class="mb-content">
                                                    <p>{{ __('language.Your are about to cancel order,are you sure') }}? .</p>
                                                </div>
                                                <div class="mb-footer buttons">
                                                    <form method="post" action="/provider/order/cancel/{{$order->type}}" class="buttons">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="order_id" value="{{$order->id}}">
                                                        <button class="btn btn-warning btn-lg btn-warning btn-lg pull-right">{{ __('language.Cancel') }}</button>
                                                    </form>
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-right: 5px;">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal animated fadeIn" id="modal_update_{{$order->id}}" tabindex="-1" role="dialog" aria-labelledby="smallModalHead" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">إغلاق</span></button>
                                                    <h4 class="modal-title" id="smallModalHead">{{ __('language.Other Expenses') }}</h4>
                                                </div>
                                                <form method="post" action="/provider/order/order_expenses" enctype="multipart/form-data">
                                                    {{csrf_field()}}
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label class="col-md-3 control-label">{{ __('language.Description') }} :</label>
                                                            <textarea rows="5" type="text" name="name" class="form-control" required></textarea>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-md-3 control-label">Cost :</label>
                                                            <input class="form-control" type="number" name="cost" required>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="order_id" value="{{$order->id}}" />
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-dark">Save</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end danger with sound -->
                                @endforeach

                                </tbody>
                            </table>


                        </div>
                    </div>
                </div>
            </div>
            {{$orders->appends($_GET)->links()}}
        </div>
    </div>
    <script>
        function myFunction(order)
        {
            // console.log(order);
            // $('#modal_order_id').val(order.id);
            // $('#modal_service_type').val(order.service_type);
            // $('#modal_cat_id').val(order.cat_id);
            //
            // if(order.service_type == 1)
            // {
            //     $('#show_service_type').hide();
            //     $('#show_field_items').hide();
            // }else{
            //     $('#show_service_type').show();
            //     $('#show_field_items').show();
            //     $('#a_tag').empty();
            //     $('#a_tag').append('<a target="_blank" href="/provider/warehouse/'+ order.cat_id +'/items">Items</a>');
            // }
            //
            // if(order.service_type == 1 || order.service_type == 2)
            // {
            //     $('#show_field_items').hide();
            // }else{
            //     $('#show_field_items').show();
            // }

            $.ajax({
                url: '/provider/get_third_category_provider/'+order.cat_id,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    $('#third_categories').empty();
                    $('#third_categories').append('<option selected disabled> Select a Third Category </option>');
                    $.each(data, function (i, third_cat) {
                        $('#third_categories').append('<option value="' + third_cat.id + '">' + third_cat.en_name + '</option>');
                    });
                    $('#third_categories').selectpicker('refresh');
                }
            });
        }

        function removeCommissionSection(x) {
            $(x).parent().parent().remove();
        }

        var commission_row_clone =  $('#first_commission_row').clone();
        function addCommissionSection(x) {
            $(commission_row_clone).clone().insertBefore($(x).parent());
        }

        $('#company_id').on('change', function () {
            var parent_id = $('#company_id').val();
            if (parent_id) {
                $.ajax({
                    url: '/provider/get_sub_company/'+parent_id,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('#sub_company').empty();
                        $('#sub_company').append('<option selected disabled> Select a Sub Company </option>');
                        $.each(data, function (i, sub_company) {
                            $('#sub_company').append('<option value="' + sub_company.id + '">' + sub_company.en_name + '</option>');
                        });
                        $('#sub_company').selectpicker('refresh');
                    }
                });

            }
        });

        $('#main_cats').on('change', function () {
            var parent_id = $('#main_cats').val();
            if (parent_id) {
                $.ajax({
                    url: '/provider/get_sub_category_provider/'+parent_id,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('#sub_cats').empty();
                        $('#sub_cats').append('<option selected disabled> Select a Sub Category </option>');
                        $.each(data, function (i, sub_cat) {
                            $('#sub_cats').append('<option value="' + sub_cat.id + '">' + sub_cat.en_name + '</option>');
                        });
                        $('#sub_cats').selectpicker('refresh');
                    }
                });

            }
        });

        $('#sub_cats').on('change', function (e) {
            var parent_id = $('#sub_cats').val();
            if (parent_id) {
                $.ajax({
                    url: '/provider/get_third_category_provider/'+parent_id,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('#third_cats').empty();
                        $('#third_cats').append('<option selected disabled> Select a Third Category </option>');
                        $.each(data, function (i, third_cat) {
                            $('#third_cats').append('<option value="' + third_cat.id + '">' + third_cat.en_name + '</option>');
                        });
                        $('#third_cats').selectpicker('refresh');
                    }
                });

            }
        });
    </script>
@endsection

@extends('provider.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/provider/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>{{ __('language.Orders') }}</li>
        <li class="active">View Order</li>
    </ul>
    <!-- END BREADCRUMB -->

    <!-- PAGE CONTENT WRAPPER -->
    <div class="page-content-wrap">

        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-default">
                    <div class="panel-body">
                        <a href="/provider/collaboration/{{$collaboration_id}}/order/{{$order->id}}/view" class="btnprn pull-right" style="font-size: 20px"> <i class="fa fa-print"></i> {{ __('language.PRINT') }}</a>
                        <script>
                            $(document).ready(function () {
                                $('.btnprn').printPage();
                            });
                        </script>
                        <h2>Order<strong> #{{$order->id}}</strong></h2>
                        @if(isset($order->smo))
                            <h2>{{ __('language.Mso NO.') }}<strong> #{{$order->smo}}</strong></h2>
                        @endif

                        <h2>{{ __('language.Category') }} : {{$order->category->parent->en_name}} - {{$order->category->en_name}}</h2>
                    {{--<div class="push-down-10 pull-right">--}}
                    {{--<button class="btn btn-default"><span class="fa fa-print"></span> Print</button>--}}
                    {{--</div>--}}
                    <!-- INVOICE -->
                        <div class="invoice">

                            <div class="row">
                                <div class="col-md-4">

                                    <div class="invoice-address">
                                        <h5>{{ __('language.User') }}</h5>
                                        <h6>{{ __('language.Company') }}: {{$order->user->company->en_name}} - {{$order->user->sub_company->en_name}}</h6>
                                        <p>{{ __('language.name') }}: {{$order->user->en_name}}</p>
                                        <p>{{ __('language.Phone') }}: {{$order->user->phone}}</p>
                                        @foreach($order->get_user_location_admin($order->user_id) as $key => $value)
                                            <p>
                                                {{$key}} : {{$value}}
                                            </p>
                                        @endforeach
                                        <p>{{ __('language.Count orders') }}: {{$count}}</p>
                                    </div>

                                </div>
                                <div class="col-md-4">

                                    <div class="invoice-address">
                                        <h5>Technician</h5>
                                        @if(isset($order->tech_id))
                                            <h6>{{ __('language.Provider') }}: {{$order->tech->provider->en_name}}</h6>
                                            <p>{{ __('language.name') }}: {{$order->tech->en_name}}</p>
                                            <p>{{ __('language.Phone') }}: {{$order->tech->phone}}</p>
                                        @else
                                            <h6>{{ __('language.Not selected yet') }}</h6>
                                        @endif
                                    </div>

                                </div>
                                <div class="col-md-4">

                                    <div class="invoice-address">
                                        <h5>Tracking</h5>
                                        @foreach($order->get_steps('en',$order->id) as $get_steps)
                                            @if($get_steps['flag'] != 0)
                                                <p>{{$get_steps['text']}}</p>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-4">
                                    <div class="invoice-address">
                                        <h5>User Extra Details</h5>
                                        @if(isset($order->user_details->place))
                                            <h6>Place</h6>
                                            <p>{{$order->user_details->place}}</p>
                                        @endif
                                        @if(isset($order->user_details->place))
                                            <h6>Part</h6>
                                            <p>{{$order->user_details->part}}</p>
                                        @endif
                                        @if(isset($order->user_details->place))
                                            <h6>{{ __('language.Description') }}</h6>
                                            <p>{{$order->user_details->desc}}</p>
                                        @endif
                                        @if(isset($order->user_details->images))
                                            <div class="gallery">
                                                @foreach(unserialize($order->user_details->images) as $image)
                                                    <a class="gallery" href="/orders/{{$image}}" title="/orders/{{$image}}" data-gallery>
                                                        <div class="image">
                                                            <img  style="width: 200px; height: 200px; display: table; margin: 0 auto;" src="/orders/{{$image}}" alt="{{$image}}"/>
                                                        </div>
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if(isset($order->tech_id) && $order->tech_details)
                                    <div class="col-md-4">
                                        <div class="invoice-address">
                                            <h5>Tech Extra Details</h5>

                                            <h6>{{ __('language.Problem Type') }}</h6>
                                            <p>{{$order->tech_details->category->parent->parent->en_name}} - {{$order->tech_details->category->parent->en_name}} - {{$order->tech_details->category->en_name}}</p>


                                            <h6>Description</h6>
                                            <p>{{$order->tech_details->desc}}</p>


                                            <h6>Before Maintenance</h6>
                                            <div class="gallery">
                                                @foreach(unserialize($order->tech_details->before_images) as $image)
                                                    <a class="gallery" href="/orders/{{$image}}" title="/orders/{{$image}}" data-gallery>
                                                        <div class="image">
                                                            <img  style="width: 200px; height: 200px; display: table; margin: 0 auto;" src="/orders/{{$image}}" alt="{{$image}}"/>
                                                        </div>
                                                    </a>
                                                @endforeach
                                            </div>

                                            <h6>After Maintenance</h6>
                                            <div class="gallery">
                                                @foreach(unserialize($order->tech_details->after_images) as $image)
                                                    <a class="gallery" href="/orders/{{$image}}" title="/orders/{{$image}}" data-gallery>
                                                        <div class="image">
                                                            <img  style="width: 200px; height: 200px; display: table; margin: 0 auto;" src="/orders/{{$image}}" alt="{{$image}}"/>
                                                        </div>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- BLUEIMP GALLERY -->
                            <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
                                <div class="slides"></div>
                                <h3 class="title"></h3>
                                <a class="prev">‹</a>
                                <a class="next">›</a>
                                <a class="close">×</a>
                                <a class="play-pause"></a>
                                <ol class="indicator"></ol>
                            </div>
                            <!-- END BLUEIMP GALLERY -->

                            @if($order->items->count() > 0)
                                <div class="table-invoice">
                                    <table class="table">
                                        <tr>
                                            <th>{{ __('language.Item Description') }}</th>
                                            <th class="text-center"> {{ __('language.Item') }}  {{ __('language.Price') }}</th>
                                            <th class="text-center">Image</th>
                                            <th class="text-center">{{ __('language.Status') }}</th>
                                            <th class="text-center">{{ __('language.Total') }}</th>
                                        </tr>
                                        @foreach($order->items as $item)
                                            <tr>
                                                <td>
                                                    <strong>{{$item->get_this_item($item->provider_id,$item->item_id)->en_name}}</strong>
                                                    <p>{{$item->get_this_item($item->provider_id,$item->item_id)->en_desc}}</p>
                                                </td>
                                                <td class="text-center">{{$item->get_this_item($item->provider_id,$item->item_id)->price}} S.R</td>
                                                <td class="text-center"><img src="/warehouses/{{$item->get_this_item($item->provider_id,$item->item_id)->image}}" class="image_radius"/></td>
                                                <td class="text-center">@if($item->status == 'confirmed') <span class="label label-success">{{ __('language.Approved') }}</span> @elseif($item->status == 'awaiting') <span class="label label-warning">Awaiting</span> @else <span class="label label-danger">{{ __('language.Declined') }}</span> @endif</td>
                                            </tr>
                                        @endforeach
                                        <th>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-center">{{$order->item_total}} S.R</td>
                                        </th>
                                    </table>
                                </div>
                            @endif

                            @if(isset($order->tech_id))
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4>Amount Due</h4>
                                        <table class="table table-striped">
                                            <tr>
                                                <td width="200"><strong>{{ __('language.Service Fee') }}:</strong></td><td class="text-right">{{$order->get_cat_fee($order->id)}} S.R</td>
                                            </tr>

                                            <tr>
                                                <td><strong>Items Total:</strong></td><td class="text-right">{{$order->item_total}} S.R</td>
                                            </tr>

                                            <tr class="total">
                                                <td>{{ __('language.Total Amount') }}:</td><td class="text-right">{{$order->order_total}} S.R</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <!-- END INVOICE -->

                    </div>
                </div>

            </div>
        </div>

    </div>
    <!-- END PAGE CONTENT WRAPPER -->


    <script>
        document.getElementById('links').onclick = function (event) {
            event = event || window.event;
            var target = event.target || event.srcElement;
            var link = target.src ? target.parentNode : target;
            var options = {index: link, event: event,onclosed: function(){
                    setTimeout(function(){
                        $("body").css("overflow","");
                    },200);
                }};
            var links = this.getElementsByTagName('a');
            blueimp.Gallery(links, options);
        };
    </script>
@endsection


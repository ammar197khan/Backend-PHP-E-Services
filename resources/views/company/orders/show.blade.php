@php
    function getStars($rate = 0){
        $grey_star = "<i style='font-size:15px' class='fa fa-star-o' aria-hidden='true'></i>";
        $gold_star = "<i style='color: #ffa800;font-size: 15px' class='fa fa-star' aria-hidden='true'></i>";

        $data = '';
        for ($i =0; $i < $rate; $i++){
            $data = $data . $gold_star;
        }
        for ($i =0; $i < 5-$rate; $i++){
            $data = $data . $grey_star;
        }
        return $data;
    }

@endphp

@extends('company.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>{{ __('language.Orders') }}</li>
        <li class="active">{{ __('language.View Order') }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    @include('admin.layouts.message')
    <!-- PAGE CONTENT WRAPPER -->
      <div class="page-content-wrap" id="GFG">

        <div class="container-fluid">
          <div class="wizard">
            <ul class="steps_4 anchor">
              @foreach($order->stages as $show)
                      @if(isset($order_tracks[$show]))
                          <li>
                              <a href="#step-1" class="selected success" isdone="1" rel="1">
                                  <span class="stepNumber">1</span>
                                  <span class="stepDesc" style="font-size:9px">
                                    {{ $show }}<br>
                                    {{$order_tracks[$show]}} <br>
                                    {{-- Took: {{ $loop->first ? 0 : $order->stages[($loop->index)-1] }} mins --}}
                                  </span>
                              </a>
                          </li>
                      @else
                        <li>
                            <a href="#step-1" class="disabled" isdone="1" rel="1">
                                <span class="stepNumber">1</span>
                                <span class="stepDesc" style="font-size:9px"> {{ $show }}<br>- {{-- <br>- --}} </span>
                            </a>
                        </li>
                      @endif
              @endforeach
            </ul>
          </div>
        </div>
        <br>

        <div class="row">
            <div class="col-md-12" >

                <div class="panel panel-default">
                    <div class="panel-heading">
                      <div class="row">
                        <h2 class="col-md-8">
                          Order<strong> #{{$order->id}}</strong>
                          @if($order->completed == 1 && $order->canceled == 0)
                              <span class="small text-success">({{ __('language.Completed') }})</span>
                          @elseif($order->completed == 0 && $order->canceled == 1)
                              @if($order->canceled_by == 'user') <span class="small text-danger">({{ __('language.Canceled By User') }})</span>
                              @elseif($order->canceled_by == 'tech') <span class="small text-danger">({{ __('language.Canceled By Technician') }})</span>
                              @else <span class="small text-danger">({{ __('language.Canceled By Admin') }})</span>
                              @endif
                          @else
                              <span class="small text-warning">({{ __('language.Open') }})</span>
                          @endif
                        </h2>
                        <div class="col-md-4">
                          <div class="row">
                            @include('admin.orders.partials.activity_log', ['history_log' => $history_log])

                            <button type="button" id="button_click" value="Print" onclick="printDiv()" class="col-md-5 btnprn pull-right btn btn-primary" style="font-size: 15px">
                              <i class="fa fa-print" style="font-size: 15px"></i>
                              {{ __('language.PRINT') }}
                            </button>

                          </div>
                        </div>
                      </div>

                    </div>
                    <div class="panel-body">
                        <div>
                            <div class="col-md-6">

                                @if(isset($order->smo))
                                    <h3>{{ __('language.Mso NO.') }}<strong> #{{$order->smo}}</strong></h3>
                                @endif
                                <h3>{{ __('language.Service Category') }} : {{ !empty($order->category) && !empty($order->category->parent) && !empty($order->category->parent->en_name) ? $order->category->parent->en_name : ''}} - {{$order->category->en_name}} </h3>
                            </div>

                            <div class="col-md-6">
                                <h3>{{ __('language.Created At') }} : {{$order->created_at}} ({{$order->created_at->diffForHumans()}})</h3>
                                @if($order->type == 'scheduled' && $order->scheduled_at != null)
                                    <h3>{{ __('language.Scheduled At') }} : {{$order->scheduled_at}} ({{isset($order->scheduled_at) ? \Carbon\Carbon::parse($order->scheduled_at)->diffForHumans() : '' }})</h3>
                                @endif
                                <h3>{{ __('language.Service Type') }} :
                                    {{-- @if ($order->service_type == 1)
                                        {{ __('language.Preview') }}
                                    @elseif ($order->service_type == 2)
                                        {{ __('language.Maintenance') }}
                                    @elseif ($order->service_type == 3)
                                         {{ __('language.Structure') }}
                                    @endif --}}
                                    {{  $order->type }}
                                </h3>
                            </div>

                        </div>

                    <!-- INVOICE -->
                        <div class="invoice">
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="invoice-address">
                                        <h5>{{ __('language.User') }}</h5>
					<h6>{{ __('language.Company') }}: {{$order->user->company ? $order->user->company->en_name:"no Company"}} - {{$order->user->sub_company ? $order->user->sub_company->en_name:"no Sub Company"}}</h6>
                                        <p>{{ __('language.Name') }}: {{$order->user->en_name}}</p>
                                        <p>{{ __('language.Phone') }}: {{$order->user->phone}}</p>
                                        <p>{{ __('language.ID') }}: {{$order->user->badge_id}}</p>
                                        @foreach($order->get_user_location_admin($order->user_id) as $key => $value)
                                            <p>
                                                {{$key}} : {{$value}}
                                            </p>
                                        @endforeach
                                        <p>{{ __('language.Count orders') }}: {{$order->user->orders->count()}}</p>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="invoice-address">
                                        <h5>{{ __('language.Technician') }}</h5>
                                        @if(isset($order->tech_id))
                                            <h6>{{ __('language.Provider') }}: {{$order->tech->provider->en_name}}</h6>
                                            <p>{{ __('language.Name') }}: {{$order->tech->en_name}}</p>
                                            <p>{{ __('language.Phone') }}: {{$order->tech->phone}}</p>
                                        @else
                                            <h6>{{ __('language.Not selected yet') }}</h6>
                                        @endif
                                    </div>
                                </div>

                                @if($order->rate)
                                    <div class="col-md-3">
                                        <h4>{{ __('language.Order Rate') }}</h4>
                                        <label class="form-label">{{ __('language.Appearance') }}</label>
                                            <div>{!!  getStars($order->rate->appearance)  !!}</div>

                                        <label class="form-label">{{ __('language.Cleanliness') }}</label>
                                        <div>{!!  getStars($order->rate->cleanliness)  !!}</div>

                                        <label class="form-label">{{ __('language.Performance') }}</label>
                                        <div>{!!  getStars($order->rate->performance)  !!}</div>

                                        <label class="form-label">{{ __('language.Commitment') }}</label>
                                        <div>{!!  getStars($order->rate->commitment)  !!}</div>
                                    </div>
                                @endif

                                <div class="col-md-3">
                                    <div class="invoice-address">
                                        <h5>{{ __('language.User Extra Details') }}</h5>
                                        @if(isset($order->user_details->place))
                                            <p>
                                              <b>{{ __('language.Place') }}: </b>
                                              {{$order->user_details->place}}
                                            </p>
                                        @endif
                                        @if(isset($order->user_details->place))
                                            <p>
                                              <b>{{ __('language.Part') }}: </b>
                                              {{$order->user_details->part}}
                                            </p>
                                        @endif
                                        @if(isset($order->user_details->place))
                                            <p>
                                              <b>{{ __('language.Description') }}: </b>
                                              {{$order->user_details->desc}}
                                            </p>
                                        @endif
                                        @if(isset($order->user_details->images))
                                            <p>
                                              <b>{{ __('language.Image') }}:</b>
                                              @foreach(unserialize($order->user_details->images) as $image)
                                                <a class="gallery" href="/orders/{{$image}}" title="/orders/{{$image}}" data-gallery>
                                                  <a href="/orders/{{$image}}" target="_blank" alt="{{$image}}"><i class="fa fa-picture-o" style="color:#2e82dd" aria-hidden="true"></i></a>
                                                </a>
                                              @endforeach
                                             </p>
                                        @endif
                                    </div>
                                </div>
@if($order->company_id == '13')
                                    <div class="col-md-4">
                                        <h4>{{ __('language.Invoice Status') }}</h4>
                                        <p>
                                        <b>{{ __('language.Payment Method') }}: </b>
                                        @if($payments != "" && $payments != null)
                                            {{$payments['payment_type']}}
                                            @endif
                                        </p>
                                        <p><b>{{ __('language.Payment date') }}: </b>
                                        @if($payments != "" && $payments != null)
                                        {{$payments['paid_at']}}
                                        @endif
                                        </p>
                                        <p>
                                        <b>{{ __('language.Payment Status') }}: </b>
                                        @if($payments != "" && $payments != null)
                                        {{ __('language.CommiPaidtment') }}
                                              @else
                                        {{ __('language.Not Paid') }}
                                              @endif
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <div class="row">

                                @if(isset($order->tech_id) && $order->tech_details)
                                    <div class="col-md-12">
                                        <div class="invoice-address">
                                            <div class="row">
                                              <h5>{{ __('language.Tech Extra Details') }}</h5>
                                                <div class="col-md-6">
                                                </div>
                                                <div class="col-md-3">
                                                    @if(isset($order->tech_details->before_images))
                                                        <p>
                                                          <b>{{ __('language.Before Maintenance Images') }}: </b>
                                                          @foreach(unserialize($order->tech_details->before_images) as $image)
                                                            <a href="/orders/{{$image}}" target="_blank" alt="{{$image}}"><i class="fa fa-picture-o" style="color:#2e82dd" aria-hidden="true"></i></a>
                                                          @endforeach
                                                        </p>
                                                    @endif
                                                </div>
                                                <div class="col-md-3">
                                                    @if(isset($order->tech_details->after_images))
                                                        <p>
                                                          <b>{{ __('language.After Maintenance Images') }}: </b>
                                                          @foreach(unserialize($order->tech_details->after_images) as $image)
                                                            <a href="/orders/{{$image}}" target="_blank" alt="{{$image}}"><i class="fa fa-picture-o" style="color:#2e82dd" aria-hidden="true"></i></a>
                                                          @endforeach
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                            <table class="table table-striped">
                                                <thead>
                                                <tr>
                                                    <th class="">{{ __('language.Problem Type') }}</th>
                                                    <th class="">{{ __('language.Price') }}</th>
                                                    <th class="">{{ __('language.Working hours') }}</th>
                                                    <th class="">{{ __('language.Description') }}</th>
                                                    <th class="">{{ __('language.Multiply') }}</th>
                                                </tr>
                                                </thead>
                                                @foreach($order->details as $tech_detail)
                                                    <tbody>
                                                    <tr>
                                                        <td>
                                                            {{$tech_detail->category->en_name}}
                                                        </td>
                                                        <td>
                                                            {{isset($tech_detail->category->cat_fee($order->company_id,$order->provider_id)->third_fee) ?
                                                            $tech_detail->category->cat_fee($order->company_id,$order->provider_id)->third_fee : 0}}
                                                        </td>
                                                        <td>
                                                            {{$tech_detail->working_hours}}
                                                        </td>
                                                        <td>{{$tech_detail->desc}}</td>
                                                        @if(isset($tech_detail->category->cat_fee($order->company_id,$order->provider_id)->third_fee))
                                                        <td>{{$tech_detail->working_hours * $tech_detail->category->cat_fee($order->company_id,$order->provider_id)->third_fee }}</td>
                                                        @else
                                                        <td>{{$tech_detail->working_hours * 0}}</td>
                                                        @endif
                                                    </tr>
                                                    </tbody>
                                                    {{-- <div class="col-md-6" id="before_maint">
                                                        @if(isset($tech_detail->before_images))
                                                            <p>
                                                              <b>Before Maintenance Images: </b>
                                                              @foreach(unserialize($tech_detail->before_images) as $image)
                                                                <a href="/orders/{{$image}}" target="_blank" alt="{{$image}}"><i class="fa fa-picture-o" style="color:#2e82dd" aria-hidden="true"></i></a>
                                                              @endforeach
                                                            </p>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-6">
                                                        @if(isset($tech_detail->after_images))
                                                          <p>
                                                            <b>After Maintenance Images: </b>
                                                            @foreach(unserialize($tech_detail->after_images) as $image)
                                                              <a href="/orders/{{$image}}" target="_blank" alt="{{$image}}"><i class="fa fa-picture-o" style="color:#2e82dd" aria-hidden="true"></i></a>
                                                            @endforeach
                                                          </p>
                                                        @endif
                                                    </div> --}}
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="row">
                              @if(isset($order->items))
                                <div class="col-md-12 invoice-address">
                                  <h5>{{ __('language.Consumed Items') }}</h5>
                                  <div class="table-invoice">
                                    <table class="table-responsive">
                                      <tr>
                                        <th class="text-center">{{ __('language.Image') }}</th>
                                        <th>{{ __('language.Item Description') }}</th>
                                        <th class="text-center">{{ __('language.Unit Price') }}</th>
                                        <th class="text-center">{{ __('language.QTY') }}</th>
                                        <th class="text-center">{{ __('language.Status') }}</th>
                                        <th class="text-center">{{ __('language.Total') }}</th>
                                        <th class="col-md-2">{{ __('language.Approve') }}</th>
                                      </tr>
                                      {{-- GET ITEMS REQUIRED APPROVAL BY USER --}}
                                      @foreach ($order->ItemsRequireUserApproval() as $item)
                                        <tr>
                                          <td><img src="/warehouses/{{$item->image}}" class="image_radius"/></a></td></td>
                                          <td>
                                            <div><strong>{{ $item->en_name}}</strong>
                                              <p>{{$item->en_desc}}</p>
                                            </div>
                                          </td>
                                          <td>{{ $item->price }} S.R</td>
                                          <td>{{ $item->taken }}</td>
                                          <td><span style="text-transform: capitalize" class="label label-warning" id="label_{{$item->id}}"{{ __('language.>Require User Approval') }}</span></td>
                                          <td><span id="item_cost_{{$item->id}}">{{ $item->price * $item->taken }}</span> S.R</td>
                                          <td>
                                            <div>
                                                <button class="btn btn-success btn-condensed" onclick="approveItem(this, {{$item->id}})">
                                                  <i class="fa fa-check" title="Approve"></i>
                                                </button>
                                                <button class="btn btn-danger btn-condensed" onclick="approveItem(this, {{$item->id}}, 'declined')">
                                                  <i class="fa fa-close" title="Decline"></i>
                                                </button>
                                            </div>
                                          </td>
                                        </tr>
                                      @endforeach

                                      {{-- GET ITEMS APPROVED BY USER --}}
                                      @foreach($order->items as $item)
                                        <tr>
                                          <td><img src="/warehouses/{{$item->get_this_item($item->provider_id,$item->item_id)->image}}" class="image_radius"/></td>
                                          <td>
                                            <div><strong>{{$item->get_this_item($item->provider_id,$item->item_id)->en_name}}</strong>
                                              <p>{{$item->get_this_item($item->provider_id,$item->item_id)->en_desc}}</p>
                                            </div>
                                          </td>
                                          <td class="text-center">{{$item->get_this_item($item->provider_id,$item->item_id)->price}} S.R</td>
                                          <td class="text-center">{{$item->taken}}</td>
                                            <td class="text-center">
                                              @if($item->status == 'confirmed')
                                                <span class="label label-success">{{ __('language.Approved') }}</span>
                                              @elseif($item->status == 'awaiting')
                                                <span class="label label-warning">{{ __('language.Require Admin Approval') }}</span>
                                              @else
                                                <span class="label label-danger">{{ __('language.Declined') }}</span>
                                              @endif
                                          </td>
                                          <td>{{ $item->taken * $item->get_this_item($item->provider_id,$item->item_id)->price }} S.R</td>
                                          <td></td>
                                        </tr>
                                      @endforeach
                                        </table>
                                      </div>
                                    </div>
                              @endif
                            </div>


                            <div class="row">
                                <div class="col-md-12">
                                    <h4>{{ __('language.Service Cost Detail') }}</h4>

                                    <table class="table table-striped">
                                        <tr>
                                            <td width="200"><strong>{{ __('language.Service Fee') }}:</strong></td><td class="text-right">{{$order->order_total}} S.R</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('language.Items Total') }}:</strong></td>
                                            <td class="text-right"><span id="order_items_amount">{{$order->item_total}}</span> S.R</td>
                                        </tr>
                                        @if(isset($order->order_expense))
                                            <tr>
                                                <td><strong>{{ __('language.Order Expenses') }}:</strong></td>
                                                <td class="text-right">{{$order->order_expense->cost}} S.R</td>
                                            </tr>
                                            <tr class="total">
                                                <td>{{ __('language.Total Amount') }}:</td>
                                                <td class="text-right"><span id="order_total_amount">{{$order->order_total + $order->item_total + $order->order_expense->cost}}</span> S.R</td>
                                            </tr>
                                        @else
                                          <tr class="total">
                                            <td>{{ __('language.Total Amount') }}:</td><td class="text-right"><span id="order_total_amount">{{$order->order_total + $order->item_total}}</span> S.R</td>
                                          </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- END INVOICE -->

                    </div>
                </div>

            </div>
        </div>

    </div>
    <!-- END PAGE CONTENT WRAPPER -->
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script type="text/javascript">
      function approveItem(button, requestID, status = 'confirmed') {
          if(!confirm('Change item status to ' + status + '?')){
            return 0;
          }
          axios.post('/ajax/admin/items/approve/' + requestID, {status : status}, {headers:{'Accept':'application/json'}})
          .then(function (response) {
              $(button).parent().hide();
              Snackbar.show({
                text: 'Item Status Changed',
                showAction: false,
                pos: 'top-right',
                backgroundColor: '#89AD4D'
              });
              $('#label_' + requestID).text(status);
              if(status == 'confirmed'){
                $('#label_' + requestID).removeClass('label-warning').addClass('label-success');
                var order_items_amount = $('#order_items_amount').text();
                var order_total_amount = $('#order_total_amount').text();
                var itemCost = $('#item_cost_' + requestID).text();
                $('#order_items_amount').text(parseFloat(order_items_amount) + parseFloat(itemCost))
                $('#order_total_amount').text(parseFloat(order_total_amount) + parseFloat(itemCost))
              } else {
                $('#label_' + requestID).removeClass('label-warning').addClass('label-danger');
              }
              console.log(response);
          })
          .catch(function (error) {
              Snackbar.show({
                text: 'OOPS .. Something Went Wrong',
                showAction: false,
                pos: 'top-right',
                backgroundColor: '#B64645'
              });
              console.log(error.response);
          });
      }
    </script>

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
    <script type="text/javascript">
        function printDiv() {
            var divContents = document.getElementById("GFG").innerHTML;
            var a = window.open('', '', 'height=900, width=900');
            a.document.write('<html>');
            a.document.write('<link rel="stylesheet" type="text/css" id="theme" href="{{asset("admin/css/theme-default.css")}}"/>');
            a.document.write('<link rel="stylesheet" type="text/css" id="theme" href="{{asset("admin/css/ion/ion.rangeSlider.css")}}"/>');
            a.document.write('<link rel="stylesheet" type="text/css" id="theme" href="{{asset("admin/css/ion/ion.rangeSlider.skinFlat.css")}}"/>');
            a.document.write('\x3Cscript type="text/javascript" src="{{asset("admin/js/plugins/jquery/jquery.min.js")}}">\x3C/script>');
            a.document.write('\x3Cscript type="text/javascript" src="{{asset("admin/js/plugins/jquery/jquery-ui.min.js")}}">\x3C/script>');
            a.document.write('\x3Cscript type="text/javascript" src="{{asset("admin/js/plugins/bootstrap/bootstrap.min.js")}}">\x3C/script>');

            a.document.write('<style>\n' +
                '        .image_radius\n' +
                '        {\n' +
                '            height: 50px;\n' +
                '            width: 50px;\n' +
                '            border: 1px solid #29B2E1;\n' +
                '            border-radius: 100px;\n' +
                '            box-shadow: 2px 2px 2px darkcyan;\n' +
                '        }\n' +
                '\n' +
                '        .input-group-addon {\n' +
                '            border-color: #33414e00 !important;\n' +
                '            background-color: #33414e00 !important;\n' +
                '            font-size: 13px;\n' +
                '            padding: 0px 0px 0px 3px;\n' +
                '            line-height: 26px;\n' +
                '            color: #FFF;\n' +
                '            text-align: center;\n' +
                '            min-width: 36px;\n' +
                '        }\n' +
                '        @media print {\n' +
                '            a[href]:after {\n' +
                '                content: none !important;\n' +
                '                text-decoration: none !important;\n' +
                '            }\n' +
                '        }\n' +
                '\n' +
                '    </style>');
            a.document.write('<body >');
            a.document.write(divContents);
            a.document.write('\x3Cscript> $("img").remove(); \x3C/script>');
            a.document.write('\x3Cscript> $("#before_maint").remove(); \x3C/script>');
            a.document.write('\x3Cscript> $("#after_maint").remove(); \x3C/script>');
            a.document.write('\x3Cscript> $("#button_click").remove(); \x3C/script>');
            {{--a.document.write('\x3Cscript> $(".rating_"+{{$order->rate->appearance}}).empty(); \x3C/script>');--}}
            a.document.write('</body></html>');
            // a.document.close();
            // a.print();
        }
        // $(document).ready(function () {
        //     $('.btnprn').printPage();
        // });
    </script>

@endsection

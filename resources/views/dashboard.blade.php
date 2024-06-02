<style>
    .compare
    {
        background-color: #c52d0b !important;
    }
    .progress-bar-danger2{

        background-color: #0057af !important;
    }
</style>
<div class="page-content-wrap" style="margin-top: 10px;">
    <!-- START WIDGETS -->
    <div class="row">

        <div class="col-md-3">
            <!-- START WIDGET -->
            <div class="widget widget-default widget-item-icon">
                <div class="widget-item-left">
                    <span class="fa fa-mail-reply"></span>
                </div>
                <div class="widget-data">
                    <div class="widget-int num-count"><a href="orders/dashboard/monthly_orders">{{$monthly_orders_count}}</a></div>
                    <div class="widget-title">{{ __('language.qareeb') }}</div>
                    <div class="widget-subtitle">
                        <a href="orders/dashboard/year_orders">{{$yearly_orders_count}}</a> In this year</div>
                </div>
            </div>
            <!-- END WIDGET -->
        </div>

        <div class="col-md-3">
            <!-- START WIDGET -->
            <div class="widget widget-default widget-item-icon">
                <div class="widget-item-left">
                    <span class="fa fa-history"></span>
                </div>
                <div class="widget-data">
                    <div class="widget-int num-count"><a href="orders/dashboard/monthly_orders_opened">{{$monthly_open}}</a></div>
                    <div class="widget-title">{{ __('language.Monthly Open Orders') }}</div>
                    <div class="widget-subtitle">Out of
                        <a href="orders/dashboard/year_orders_opened">{{$yearly_open}}</a> In this year</div>
                </div>
            </div>
            <!-- END WIDGET -->
        </div>


        <div class="col-md-3">
            <!-- START WIDGET -->
            <div class="widget widget-default widget-item-icon">
                <div class="widget-item-left">
                    <span class="fa fa-check-circle"></span>
                </div>
                <div class="widget-data">
                    <div class="widget-int num-count"><a href="orders/dashboard/monthly_orders_closed">{{$monthly_closed}}</a></div>
                    <div class="widget-title">Monthly Closed Orders</div>
                    <div class="widget-subtitle">Out of
                        <a href="orders/dashboard/year_orders_closed">{{$yearly_closed}}</a> In this year</div>
                </div>
            </div>
            <!-- END WIDGET -->
        </div>

        <div class="col-md-3">
            <!-- START WIDGET -->
            <div class="widget widget-default widget-item-icon">
                <div class="widget-item-left">
                    <span class="fa fa-times-circle"></span>
                </div>
                <div class="widget-data">
                    <div class="widget-int num-count"><a href="orders/dashboard/monthly_orders_canceled">{{$monthly_canceled}}</a></div>
                    <div class="widget-title">{{ __('language.Monthly Canceled Orders') }}</div>
                    <div class="widget-subtitle">Out of
                        <a href="orders/dashboard/year_orders_canceled">{{$yearly_canceled}}</a> In this year</div>
                </div>
            </div>
            <!-- END WIDGET -->
        </div>


    </div>
    <!-- END WIDGETS -->
    <div class="row">
        <div class="col-md-6">

            <!-- START SALES BLOCK -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title-box">
                        <h3>Maintenance Orders</h3>
                        <span>Activities since {{$this_month->format('l j F Y')}}</span>
                    </div>
                    <ul class="panel-controls panel-controls-title">
                        <li><a href="#" class="panel-fullscreen rounded"><span class="fa fa-expand"></span></a></li>
                    </ul>

                </div>
                <div class="panel-body">
                    <div class="row stacked">
                        <div class="col-md-12">
                            <div class="progress-list">
                                <div class="pull-left"><strong>On going</strong></div>
                                <div class="pull-right">{{$monthly_open}}</div>
                                <div class="progress progress-small progress-striped @if($monthly_open > 0) {{ __('language.Active') }} @endif">
                                    <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">100%</div>
                                </div>
                            </div>
                            <div class="progress-list">
                                <div class="pull-left"><strong>Completion Rate</strong></div>
                                @if($monthly_orders_count > 0)
                                    <div class="pull-right">{{$monthly_closed}}/{{$monthly_orders_count}} as {{round($monthly_closed / $monthly_orders_count * 100, 1)}}%</div>
                                    <div class="progress progress-small progress-striped">
                                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: {{$monthly_closed / $monthly_orders_count * 100}}%;">{{$monthly_closed / $monthly_orders_count * 100}}%</div>
                                    </div>
                                @else
                                    <div class="pull-right">{{$monthly_closed}}/0 as 0%</div>
                                    <div class="progress progress-small progress-striped">
                                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">0%</div>
                                    </div>
                                @endif
                            </div>
                            <div class="progress-list">
                                <div class="pull-left"><strong class="text-danger">Canceled Orders</strong></div>

                                @if($monthly_orders_count > 0)
                                    <div class="pull-right">{{$monthly_canceled}}/{{$monthly_orders_count}} as {{round($monthly_canceled / $monthly_orders_count * 100, 1)}}%</div>
                                    <div class="progress progress-small progress-striped">
                                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: {{$monthly_canceled / $monthly_orders_count * 100}}%;">{{$monthly_canceled / $monthly_orders_count * 100}}%</div>
                                    </div>
                                @else
                                    <div class="pull-right">0/0 as 0%</div>
                                    <div class="progress progress-small progress-striped">
                                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">0%</div>
                                    </div>
                                @endif

                            </div>
                            <div class="progress-list">
                                <div class="pull-left"><strong class="text-danger">Declination Ratio</strong></div><br/>
                                <div class="pull-left">Technician ( {{$monthly_canceled_tech}} )</div>
                                <div class="pull-right">( {{$monthly_canceled_user}} ) User</div>
                                <div class="progress progress compare">
                                    @if($monthly_orders_count > 0 && $monthly_canceled > 0)
                                        <div class="progress-bar progress-bar-danger progress-bar-danger2" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: {{$monthly_canceled_tech / $monthly_canceled * 100}}%;"></div>
                                    @else
                                        <div class="progress-bar progress-bar-danger progress-bar-danger2" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
                                    @endif                                        </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div id="dashboard-map-seles" style="width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END SALES BLOCK -->
        </div>
        <div class="col-md-6">

            <!-- START SALES BLOCK -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title-box">
                        <h3>Maintenance Orders</h3>
                        <span>Activities in {{$this_year->format('Y')}}</span>
                    </div>
                    <ul class="panel-controls panel-controls-title">
                        <li><a href="#" class="panel-fullscreen rounded"><span class="fa fa-expand"></span></a></li>
                    </ul>

                </div>
                <div class="panel-body">
                    <div class="row stacked">
                        <div class="col-md-12">
                            <div class="progress-list">
                                <div class="pull-left"><strong>On going</strong></div>
                                <div class="pull-right">{{$yearly_open}}</div>
                                <div class="progress progress-small progress-striped @if($yearly_open > 0) {{ __('language.Active') }} @endif">
                                    <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">100%</div>
                                </div>
                            </div>
                            <div class="progress-list">
                                <div class="pull-left"><strong>Completion Rate</strong></div>
                                @if($yearly_orders_count > 0)
                                    <div class="pull-right">{{$yearly_closed}}/{{$yearly_orders_count}} as {{round($yearly_closed / $yearly_orders_count * 100, 1)}}%</div>
                                    <div class="progress progress-small progress-striped">
                                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: {{$yearly_closed / $yearly_orders_count * 100}}%;">{{$yearly_closed / $yearly_orders_count * 100}}%</div>
                                    </div>
                                @else
                                    <div class="pull-right">0 as 0%</div>
                                    <div class="progress progress-small progress-striped">
                                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">0%</div>
                                    </div>
                                @endif
                            </div>
                            <div class="progress-list">
                                <div class="pull-left"><strong class="text-danger">Canceled Orders</strong></div>

                                @if($yearly_orders_count > 0)
                                    <div class="pull-right">{{$yearly_canceled}}/{{$yearly_orders_count}} as {{round($yearly_canceled / $yearly_orders_count * 100, 1)}}%</div>
                                    <div class="progress progress-small progress-striped">
                                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: {{$yearly_canceled / $yearly_orders_count * 100}}%;">{{$yearly_canceled / $yearly_orders_count * 100}}%</div>
                                    </div>
                                @else
                                    <div class="pull-right">{{$yearly_canceled}}/0 as 0%</div>
                                    <div class="progress progress-small progress-striped">
                                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">0%</div>
                                    </div>
                                @endif

                            </div>
                            <div class="progress-list">
                                <div class="pull-left"><strong class="text-danger">Declination Ratio</strong></div><br/>
                                <div class="pull-left">Technician ( {{$yearly_canceled_tech}} )</div>
                                <div class="pull-right">( {{$yearly_canceled_user}} ) User</div>
                                <div class="progress progress compare">
                                    @if($yearly_orders_count > 0 && $yearly_canceled > 0)
                                        <div class="progress-bar progress-bar-danger progress-bar-danger2" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: {{$yearly_canceled_tech / $yearly_canceled * 100}}%;"></div>
                                    @else
                                        <div class="progress-bar progress-bar-danger progress-bar-danger2" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
                                    @endif                                        </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div id="dashboard-map-seles" style="width: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END SALES BLOCK -->

    </div>
    <!-- START WIDGETS -->
    <div class="row">

        <div class="col-md-3">
            <!-- START WIDGET -->
            <div class="widget widget-default widget-item-icon">
                <div class="widget-item-left">
                    <span class="fa fa-mail-reply"></span>
                </div>
                <div class="widget-data">
                    <div class="widget-int num-count"><a href="orders/monthly_parts_orders_count">{{$monthly_parts_orders_count}}</a></div>
                    <div class="widget-title">Monthly Orders With Spare parts</div>
                    <div class="widget-subtitle"><a href="orders/yearly_parts_orders_count">
                            {{$yearly_parts_orders_count}}</a> In this year</div>
                </div>
            </div>
            <!-- END WIDGET -->
        </div>

        <div class="col-md-3">
            <!-- START WIDGET -->
            <div class="widget widget-default widget-item-icon">
                <div class="widget-item-left">
                    <span class="fa fa-cubes"></span>
                </div>
                <div class="widget-data">
                    <div class="widget-int num-count"><a href="items/dashboard/monthly_parts_count">{{$monthly_parts_count}}</a></div>
                    <div class="widget-title">Monthly Spare Parts Requested</div>
                    <div class="widget-subtitle">Out of <a href="items/dashboard/yearly_parts_count">
                            {{$yearly_parts_count}}</a> In this year</div>
                </div>
            </div>
            <!-- END WIDGET -->
        </div>


        <div class="col-md-3">
            <!-- START WIDGET -->
            <div class="widget widget-default widget-item-icon">
                <div class="widget-item-left">
                    <span class="fa fa-money"></span>
                </div>
                <div class="widget-data">
                    <div class="widget-int num-count"><a href="price/dashboard/monthly_parts_prices">{{$monthly_parts_prices}} S.R</a></div>
                    <div class="widget-title">Monthly Spare Parts Total Price</div>
                    <div class="widget-subtitle">Out of <a href="price/dashboard/yearly_parts_prices">
                            {{$yearly_parts_prices}}</a> S.R In this year</div>
                </div>
            </div>
            <!-- END WIDGET -->
        </div>

        <div class="col-md-3">
            <!-- START WIDGET -->
            <div class="widget widget-default widget-item-icon">
                <div class="widget-item-left">
                    <span class="fa fa-money"></span>
                </div>
                <div class="widget-data">
                    <div class="widget-int num-count"><a href="price/dashboard/monthly_revenue">{{$monthly_revenue}}</a></div>
                    <div class="widget-title">Monthly Orders Revenues</div>
                    <div class="widget-subtitle">Out of <a href="price/dashboard/yearly_revenue">
                            {{$yearly_revenue}}</a> In this year</div>
                </div>
            </div>
            <!-- END WIDGET -->
        </div>


        <div class="col-md-3">
            <!-- START WIDGET MESSAGES -->
            <div class="widget widget-default widget-item-icon">
                <div class="widget-item-left">
                    <span class="fa fa-thumbs-up"></span>
                </div>
                <div class="widget-data">
                    <a class="widget-int num-count" href="rate/monthly_rate">{{$monthly_rate_commitment}} of 5</a>
                    <div class="widget-title link">{{ __('language.Commitment') }} </div>
                    <a class="widget-subtitle link" href="rate/yearly_rate">{{$yearly_rate_commitment}} of 5 in this year</a>
                </div>
            </div>
            <!-- END WIDGET MESSAGES -->
        </div>


        <div class="col-md-3">
            <!-- START WIDGET MESSAGES -->
            <div class="widget widget-default widget-item-icon">
                <div class="widget-item-left">
                    <span class="fa fa-thumbs-up"></span>
                </div>
                <div class="widget-data">
                    <a class="widget-int num-count" href="rate/monthly_rate">{{$monthly_rate_appearance}} of 5</a>
                    <div class="widget-title link">{{ __('language.Appearance') }}</div>
                    <a class="widget-subtitle link" href="rate/yearly_rate">{{$yearly_rate_appearance}} of 5 in this year</a>
                </div>
            </div>
            <!-- END WIDGET MESSAGES -->
        </div>


        <div class="col-md-3">
            <!-- START WIDGET MESSAGES -->
            <div class="widget widget-default widget-item-icon">
                <div class="widget-item-left">
                    <span class="fa fa-thumbs-up"></span>
                </div>
                <div class="widget-data">
                    <a class="widget-int num-count" href="rate/monthly_rate">
                        {{$monthly_rate_performance}} of 5</a>
                    <div class="widget-title link">{{ __('language.Performance') }}</div>
                    <a class="widget-subtitle link" href="rate/yearly_rate">
                        {{$yearly_rate_cleanliness}} of 5 in this year</a>
                </div>
            </div>
            <!-- END WIDGET MESSAGES -->
        </div>


        <div class="col-md-3">
            <!-- START WIDGET MESSAGES -->
            <div class="widget widget-default widget-item-icon">
                <div class="widget-item-left">
                    <span class="fa fa-thumbs-up"></span>
                </div>
                <div class="widget-data">
                    <a class="widget-int num-count" href="rate/monthly_rate">
                        {{$monthly_rate_cleanliness}} of 5</a>
                    <div class="widget-title link">{{ __('language.Cleanliness') }}</div>
                    <a class="widget-subtitle link" href="rate/yearly_rate">
                        {{$monthly_rate_cleanliness}} of 5 in this year</a>
                </div>
            </div>
            <!-- END WIDGET MESSAGES -->
        </div>


    </div>

    @if(count($orders) > 0)
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('language.Order No') }}.</th>
                    <th>{{ __('language.Mso NO.') }}</th>
                    <th>{{ __('language.Type') }}</th>
                    {{--<th>Badge ID</th>--}}
                    <th>Technician</th>
                    <th>{{ __('language.User') }}</th>
                    <th>{{ __('language.Date') }}</th>
                    <th>{{ __('language.Status') }}</th>
                    {{--<th>Items</th>--}}
                    <th>{{ __('language.Operations') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>{{isset($order->id) ? $order->id : '-'}}</td>
                        <td>{{isset($order->smo) ? $order->smo : '-'}}</td>
                        <td>{{$order->type}}</td>
                        {{--<td>@if(isset($order->tech_id))  {{$order->tech->badge_id}} @else Not selected yet @endif</td>--}}
                        <td>{{isset($order->tech_id) ? $order->tech->en_name : 'Not selected yet'}}</td>
                        <td>{{$order->user->en_name}}</td>
                        <td>
                            @if($order->type == 'urgent')
                                {{$order->created_at}}
                            @elseif($order->type == 'scheduled')
                                {{$order->scheduled_at}}
                            @else
                                {{isset($order->scheduled_at) ? $order->scheduled_at : 'Not selected yet'}}
                            @endif
                        </td>
                        <td>@if($order->completed == 1 && $order->canceled == 0) <span class="label label-success">{{ __('language.Complete') }}</span> @elseif($order->completed == 0 && $order->canceled == 1) @if($order->canceled_by == 'user') <span class="label label-danger">{{ __('language.Canceled By User') }}</span> @elseif($order->canceled_by == 'tech') <span class="label label-danger">{{ __('language.Canceled By Technician') }}</span> @else <span class="label label-danger">{{ __('language.Canceled By Admin') }}</span> @endif @else <span class="label label-primary">Open</span> @endif</td>
                        {{--<td>{{$order->items->count()}}</td>--}}
                        <td>
                            <a title="View" href="/company/order/{{$order->id}}/view"><button class="btn btn-info btn-condensed"><i class="fa fa-eye"></i></button></a>
                        </td>
                    </tr>

                @endforeach

                </tbody>
            </table>
        </div>
    </div>
    @endif
    <!-- END WIDGETS -->
</div>
<!-- START THIS PAGE PLUGINS-->
<script type='text/javascript' src='{{asset('admin/js/plugins/icheck/icheck.min.js')}}'></script>
<script type="text/javascript" src="{{asset('admin/js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js')}}"></script>
<script type="text/javascript" src="{{asset('admin/js/plugins/scrolltotop/scrolltopcontrol.js')}}"></script>

<script type="text/javascript" src="{{asset('admin/js/plugins/morris/raphael-min.js')}}"></script>
<script type="text/javascript" src="{{asset('admin/js/plugins/morris/morris.min.js')}}"></script>
<script type="text/javascript" src="{{asset('admin/js/plugins/rickshaw/d3.v3.js')}}"></script>
<script type="text/javascript" src="{{asset('admin/js/plugins/rickshaw/rickshaw.min.js')}}"></script>
<script type='text/javascript' src='{{asset('admin/js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js')}}'></script>
<script type='text/javascript' src='{{asset('admin/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js')}}'></script>
<script type='text/javascript' src='{{asset('admin/js/plugins/bootstrap/bootstrap-datepicker.js')}}'></script>
<script type="text/javascript" src="{{asset('admin/js/plugins/owl/owl.carousel.min.js')}}"></script>

<script type="text/javascript" src="{{asset('admin/js/plugins/moment.min.js')}}"></script>
<script type="text/javascript" src="{{asset('admin/js/plugins/daterangepicker/daterangepicker.js')}}"></script>
<!-- END THIS PAGE PLUGINS-->

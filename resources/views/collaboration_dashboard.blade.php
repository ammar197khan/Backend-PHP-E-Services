
<style>
    .compare
    {
        background-color: #c52d0b !important;
    }
    .progress-bar-danger2{

        background-color: #0057af !important;
    }
</style>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="dashboard">{{ __('language.Dashboard') }}</a></li>
    <li><a href="collaborations">{{ __('language.Collaborations') }}</a></li>
    {{--<li>{{$collaboration->company->en_name}}</li>--}}
    <li class="active">{{ __('language.Statistics') }}</li>
</ul>
<!-- END BREADCRUMB -->

<div class="page-content-wrap">
    <!-- START WIDGETS -->
    <div class="row">

        <div class="col-md-3">
            <!-- START WIDGET -->
            <div class="widget widget-default widget-item-icon">
                <div class="widget-item-left">
                    <span class="fa fa-mail-reply"></span>
                </div>
                <div class="widget-data">
                    <div class="widget-int num-count"><a href="statistics/monthly_orders">{{$monthly_orders_count}}</a></div>
                    <div class="widget-title">{{ __('language.qareeb') }}</div>
                    <div class="widget-subtitle">
                        <a href="statistics/year_orders">{{$yearly_orders_count}}</a> In this year</div>
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
                    <div class="widget-int num-count"><a href="statistics/monthly_orders_opened">{{$monthly_open}}</a></div>
                    <div class="widget-title"> {{ __('language.Monthly Open Orders') }}</div>
                    <div class="widget-subtitle">Out of
                        <a href="statistics/year_orders_opened">{{$yearly_open}}</a> In this year</div>
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
                    <div class="widget-int num-count"><a href="statistics/monthly_orders_closed">{{$monthly_closed}}</a></div>
                    <div class="widget-title">Monthly Closed Orders</div>
                    <div class="widget-subtitle">Out of
                        <a href="statistics/year_orders_closed">{{$yearly_closed}}</a> In this year</div>
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
                    <div class="widget-int num-count"><a href="statistics/monthly_orders_canceled">{{$monthly_canceled}}</a></div>
                    <div class="widget-title">{{ __('language.Monthly Canceled Orders') }}</div>
                    <div class="widget-subtitle">Out of
                        <a href="statistics/year_orders_canceled">{{$yearly_canceled}}</a> In this year</div>
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
                                <div class="progress progress-small progress-striped @if($monthly_open > 0) zive @endif">
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
                    <div class="widget-int num-count"><a href="statistics/monthly_parts_orders_count">
                            {{$monthly_parts_orders_count}}</a></div>
                    <div class="widget-title" id="">Monthly Orders With Spare parts</div>
                    <div class="widget-subtitle"><a href="statistics/yearly_parts_orders_count">
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
                    <div class="widget-int num-count"><a href="statistics/items/monthly_parts_count">{{$monthly_parts_count}}</a></div>
                    <div class="widget-title">Monthly Spare Parts Requested</div>
                    <div class="widget-subtitle">Out of <a href="statistics/items/yearly_parts_count">
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
                    <div class="widget-int num-count"><a href="statistics/price/monthly_parts_prices">
                            {{$monthly_parts_prices}} S.R</a></div>
                    <div class="widget-title">Monthly Spare Parts Total Price</div>
                    <div class="widget-subtitle">Out of <a href="statistics/price/yearly_parts_prices">
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
                    <div class="widget-int num-count"><a href="statistics/price/monthly_revenue">
                            {{$monthly_revenue}}</a></div>
                    <div class="widget-title">Monthly Orders Revenues</div>
                    <div class="widget-subtitle">Out of <a href="statistics/price/yearly_revenue">
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
                    <a class="widget-int num-count" href="statistics/rate/monthly_rate">{{$monthly_rate_commitment}} of 5</a>
                    <div class="widget-title link">{{ __('language.Commitment') }}</div>
                    <a class="widget-subtitle link" href="statistics/rate/yearly_rate">{{$yearly_rate_commitment}} of 5 in this year</a>
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
                    <a class="widget-int num-count" href="statistics/rate/monthly_rate">{{$monthly_rate_appearance}} of 5</a>
                    <div class="widget-title link"> {{ __('language.Appearance') }}</div>
                    <a class="widget-subtitle link" href="statistics/rate/yearly_rate">{{$yearly_rate_appearance}} of 5 in this year</a>
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
                    <a class="widget-int num-count" href="statistics/rate/monthly_rate">{{$monthly_rate_performance}} of 5</a>
                    <div class="widget-title link"> {{ __('language.Performance') }}</div>
                    <a class="widget-subtitle link" href="statistics/rate/yearly_rate">{{$yearly_rate_cleanliness}} of 5 in this year</a>
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
                    <a class="widget-int num-count" href="statistics/rate/monthly_rate">{{$monthly_rate_cleanliness}} of 5</a>
                    <div class="widget-title link"> {{ __('language.Cleanliness') }}</div>
                    <a class="widget-subtitle link" href="statistics/rate/yearly_rate">{{$monthly_rate_cleanliness}} of 5 in this year</a>
                </div>
            </div>
            <!-- END WIDGET MESSAGES -->
        </div>


    </div>
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

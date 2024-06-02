<!DOCTYPE html>
<html lang="en">
<head>
    <!-- META SECTION -->
    <title>Qareeb - Provider Dashboard</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="icon" href="{{asset('admin/assets/images/users/avatar.jpg')}}" type="image/x-icon" />
    <link rel="stylesheet" type="text/css" href="{{asset('admin/css/ion/ion.rangeSlider.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('admin/css/ion/ion.rangeSlider.skinFlat.css')}}"/>
    <!-- END META SECTION -->

    <!-- CSS INCLUDE -->
    <link rel="stylesheet" type="text/css" id="theme" href="{{asset('admin/css/theme-default.css')}}"/>
    <script type="text/javascript" src="{{asset('admin/js/plugins/jquery/jquery.min.js')}}"></script>
    <!-- START PLUGINS -->
    <script type="text/javascript" src="{{asset('admin/js/plugins/jquery/jquery-ui.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin/js/plugins/bootstrap/bootstrap.min.js')}}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">

    <!-- END PLUGINS -->
    <!-- EOF CSS INCLUDE -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="{{ asset("admin/js/Chart.min.js") }}"></script>
    <script src="{{ asset("admin/js/utils.js") }}"></script>

    <style>
        .image_radius
        {
            height: 50px;
            width: 50px;
            border: 1px solid #29B2E1;
            border-radius: 100px;
            box-shadow: 2px 2px 2px darkcyan;
        }

        .input-group-addon {
            border-color: #33414e00 !important;
            background-color: #33414e00 !important;
            font-size: 13px;
            padding: 0px 0px 0px 3px;
            line-height: 26px;
            color: #FFF;
            text-align: center;
            min-width: 36px;
        }
        .link a:hover
        {
            text-decoration: none;
        }

    </style>

@if(\Session::get('current_locale',config('app.fallback_locale','en')) == 'ar')
<style>
body{
    direction: rtl;
    text-align: right;
}
.page-container .page-content{
    margin-right: 220px !important;
    margin-left: 0px !important;
}
.page-container .page-sidebar{
    float: right !important;
}
.page-title h2{
    float: right !important;
}
.x-navigation li{
    float: left !important;
}
.control-label{
    float:right !important;
    text-align: left !important;
}
.col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9 {
 float: right!important;
}
.btn {
    float: left !important;
   }
.panel .panel-title {
    float: right !important;
  }
  .profile .profile-controls a.profile-control-left {
    right: 15px!important;
  }
  .dropdown-menu {
    text-align: right!important ;
    margin-right: -156px!important;
  }
  .message-box .mb-container .mb-middle .mb-title .fa, .message-box .mb-container .mb-middle .mb-title .glyphicon {
    float: right!important;
  }
  .message-box .mb-container .mb-middle {
    right: 25%!important;
  }
</style>
@endif
</head>
<body>
<!-- START PAGE CONTAINER -->
<div class="page-container page-mode-ltr page-content-ltr">
    <!-- START PAGE SIDEBAR -->
    <div class="page-sidebar page-sidebar-fixed scroll">
        <!-- START X-NAVIGATION -->
        <ul class="x-navigation">
            <li class="xn-logo">
                <a href="/provider/dashboard">Qareeb - Provider Dashboard</a>
                <a href="#" class="x-navigation-control"></a>
            </li>

            <li class="xn-profile">
                <div class="profile">
                    <div class="profile-image">
                    @php
                    $logo = "";
                    $providerID= provider()->provider_id;
                    $findLogo = DB::table('providers')->where('id', $providerID)->first();
                    if($findLogo && $findLogo != ""){ $logo = $findLogo->logo; }
                    @endphp
                    <img src="/providers/logos/{{$logo}}" alt="Qareeb"/>
                    </div>
                    <div class="profile-controls">
                        <a href="/provider/profile" class="profile-control-left" title="View Profile"><span class="fa fa-user"></span></a>
                    </div>
                </div>
            </li>

            <li @if(Request::is('provider/dashboard')) class="active" @endif>
                <a href="/provider/dashboard"><span class="fa fa-dashboard"></span><span class="xn-text">{{ __('language.Dashboard') }}</span></a>
            </li>

            @if(
                provider()->hasPermissionTo('View provider info'))
                <li @if(Request::is('provider/info')) class="active" @endif>
                    <a href="/provider/info"><span class="fa fa-info-circle"></span><span class="xn-text">{{ __('language.Provider Profile') }}</span></a>
                </li>
            @endif

            @if(provider()->hasPermissionTo('View admin'))
                <li @if(Request::is('provider/admins/*')) class="active" @endif>
                    <a href="/provider/admins/index"><span class="fa fa-user-secret"></span><span class="xn-text">{{ __('language.Admins') }}</span></a>
                </li>
            @endif

            {{--<li @if(Request::is('provider/services/fees/view')) class="active" @endif>--}}
                {{--<a href="/provider/services/fees/view"><span class="fa fa-money"></span><span class="xn-text">Services Fees</span></a>--}}
            {{--</li>--}}

            {{--<li @if(Request::is('provider/third/fees/view')) class="active" @endif>--}}
                {{--<a href="/provider/third/fees/view"><span class="fa fa-usd"></span><span class="xn-text">Third Category Fees</span></a>--}}
            {{--</li>--}}

            @if(provider()->hasPermissionTo('View collaboration'))
                <li @if(Request::is('provider/collaborations') xor Request::is('provider/collaboration/*')) class="active" @endif>
                    <a href="/provider/collaborations"><span class="fa fa-handshake-o"></span><span class="xn-text">{{ __('language.Partnership') }}</span></a>
                </li>
            @endif

            @if(provider()->hasPermissionTo('View collaboration'))
                <li @if(Request::is('provider/invoices') xor Request::is('provider/invoices/*')) class="active" @endif>
                    <a href="/provider/invoices"><span class="fa fa-bitcoin"></span><span class="xn-text">{{ __('language.Invoices') }}</span></a>
                </li>
            @endif

            @if(provider()->hasPermissionTo('View orders'))
                <li @if(Request::is('provider/orders/*')) class="active" @endif>
                    <a href="/provider/orders/all"><span class="fa fa-truck"></span><span class="xn-text">{{ __('language.Orders') }}</span></a>
                </li>
            @endif

{{--            @if(provider()->hasPermissionTo('View orders'))--}}
{{--                <li class="xn-openable @if(Request::is('provider/orders/*') xor Request::is('provider/order/*')) active @endif">--}}
{{--                    <a href="#"><span class="fa fa-truck"></span><span class="xn-text">Orders</span></a>--}}
{{--                    <ul>--}}
{{--                        <li @if(Request::is('provider/orders/urgent')) class="active" @endif>--}}
{{--                            <a href="/provider/orders/urgent"><span class="fa fa-check-square"></span><span class="xn-text">Urgent</span></a>--}}
{{--                        </li>--}}
{{--                        <li @if(Request::is('provider/orders/scheduled')) class="active" @endif>--}}
{{--                            <a href="/provider/orders/scheduled"><span class="fa fa-clock-o"></span><span class="xn-text">Scheduled</span></a>--}}
{{--                        </li>--}}
{{--                        <li @if(Request::is('provider/orders/re_scheduled')) class="active" @endif>--}}
{{--                            <a href="/provider/orders/re_scheduled"><span class="fa fa-minus-circle"></span><span class="xn-text">Re-Scheduled</span></a>--}}
{{--                        </li>--}}
{{--                        <li @if(Request::is('provider/orders/canceled')) class="active" @endif>--}}
{{--                            <a href="/provider/orders/canceled"><span class="fa fa-times-circle"></span><span class="xn-text">Canceled</span></a>--}}
{{--                        </li>--}}
{{--                        <li @if(Request::is('provider/orders/waiting')) class="active" @endif>--}}
{{--                            <a href="/provider/orders/open/waiting"><span class="fa fa-hourglass"></span><span class="xn-text">Waiting publish</span></a>--}}
{{--                        </li>--}}
{{--                    </ul>--}}
{{--                </li>--}}
{{--            @endif--}}


            @if(provider()->hasPermissionTo('View warehouse'))
                <li @if(Request::is('provider/warehouse/*')) class="active" @endif>
                    <a href="/provider/warehouse/all"><span class="fa fa-cubes"></span><span class="xn-text">{{ __('language.Warehouse') }}</span></a>
                </li>
            @endif

            @if(provider()->hasPermissionTo('View warehouse request'))
                <li @if(Request::is('provider/warehouse_requests/*')) class="active" @endif>
                    <a href="/provider/warehouse_requests"><span class="fa fa-question-circle-o"></span><span class="xn-text">{{ __('language.Warehouse Requests') }}</span></a>
                </li>
            @endif

            @if(provider()->hasPermissionTo('View technician'))
                <li class="xn-openable @if(Request::is('provider/technicians/*') xor Request::is('provider/technician/*')) active @endif" >
                    <a href="#"><span class="fa fa-wrench"></span><span class="xn-text">{{ __('language.Technicians') }}</span></a>
                    <ul>
                        <li @if(Request::is('provider/technicians/active')) class="active" @endif>
                            <a href="/provider/technicians/active"><span class="fa fa-check-square"></span><span class="xn-text">{{ __('language.Active') }}</span></a>
                        </li>
                        <li @if(Request::is('provider/technicians/suspended')) class="active" @endif>
                            <a href="/provider/technicians/suspended"><span class="fa fa-minus-square"></span><span class="xn-text">{{ __('language.Suspended') }}</span></a>
                        </li>
                        @if(provider()->hasPermissionTo('Statistics technician'))
                            <li @if(Request::is('provider/technicians/statistics')) class="active" @endif>
                                <a href="/provider/technicians/statistics"><span class="fa fa-area-chart"></span><span class="xn-text">{{ __('language.Statistics') }}</span></a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if(provider()->hasPermissionTo('View rotation'))
                <li @if(Request::is('provider/rotations/*')) class="active" @endif>
                    <a href="/provider/rotations/index"><span class="fa fa-repeat"></span><span class="xn-text">Rotations</span></a>
                </li>
            @endif

        </ul>
        <!-- END X-NAVIGATION -->
    </div>
    <!-- END PAGE SIDEBAR -->

    <!-- PAGE CONTENT -->
    <div class="page-content">

        <!-- START X-NAVIGATION VERTICAL -->
        <ul class="x-navigation x-navigation-horizontal x-navigation-panel">
            <!-- POWER OFF -->
            <li class="xn-icon-button pull-right last">
                <a href="#" class="mb-control" data-box="#mb-signout" title="Logout"><span class="fa fa-power-off"></span></a>
            </li>
            <li class="nav-item pull-right dropdown">

<a class="nav-link dropdown-toggle dropdown-translate" href="javascript:void(0);" id="navbarDarkDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
  <i class="fa fa-globe "></i> {{ app()->getLocale() == 'en' ? 'English': 'Arabic' }}
</a>
<ul class="dropdown-menu translate-select dropdown-menu-dark " aria-labelledby="navbarDarkDropdownMenuLink" style="min-width: 142px!important; width: 142px;">
  <li><a class="dropdown-item {{app()->getLocale() == 'ar' ? 'active' : ''}}" href="javascript:void(0);">English</a></li>
  <li><a class="dropdown-item {{app()->getLocale() == 'ar' ? 'active' : ''}}" href="javascript:void(0);">Arabic</a></li>
</ul>
</li>
            <!-- END POWER OFF -->
        </ul>
        <!-- END X-NAVIGATION VERTICAL -->

        <!-- MESSAGE BOX-->
        <div class="message-box animated fadeIn" data-sound="alert" id="mb-signout">
            <div class="mb-container">
                <div class="mb-middle">
                    <div class="mb-title"><span class="fa fa-sign-out"></span> Log <strong>Out</strong> ?</div>
                    <div class="mb-content">
                        <p>Are you sure you want to log out?</p>
                        <p>Press No if you want to continue work. Press Yes to logout current user.</p>
                    </div>
                    <div class="mb-footer">
                        <div class="pull-right">
                            <a href="/provider/logout" class="btn btn-success btn-lg">Yes</a>
                            <button class="btn btn-default btn-lg mb-control-close">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END MESSAGE BOX-->

    @yield('content')




    <script type="text/javascript">

       var APP_URL = {!! json_encode(url('/')) !!}

    </script>
    <script>
        function isEmpty(value) {
            let response = true;
            if (value != null && value != "null" && value != "undefined" && value != "") {
                response = false;
            }
            return response;
        }

    </script>
    <!-- START PRELOADS -->
        <audio id="audio-alert" src="{{asset('admin/audio/alert.mp3')}}" preload="auto"></audio>
        <audio id="audio-fail" src="{{asset('admin/audio/fail.mp3')}}" preload="auto"></audio>
        <!-- END PRELOADS -->


        <!-- THIS PAGE PLUGINS -->
        <script type='text/javascript' src="{{asset('admin/js/plugins/icheck/icheck.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('admin/js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js')}}"></script>

        <script type='text/javascript' src='{{asset('admin/js/plugins/icheck/icheck.min.js')}}'></script>
        <script type="text/javascript" src="{{asset('admin/js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js')}}"></script>

        <script type="text/javascript" src="{{asset('admin/js/plugins/datatables/jquery.dataTables.min.js')}}"></script>

        <script type="text/javascript" src="{{asset('admin/js/plugins/owl/owl.carousel.min.js')}}"></script>
        <!-- END PAGE PLUGINS -->

        <!-- START TEMPLATE -->
        <script type="text/javascript" src="{{asset('admin/js/jquery.printPage.js')}}"></script>
        <script type="text/javascript" src="{{asset('admin/js/plugins.js')}}"></script>
        <script type="text/javascript" src="{{asset('admin/js/actions.js')}}"></script>

        <script type="text/javascript" src="{{asset('admin/js/plugins/bootstrap/bootstrap-datepicker.js')}}"></script>
        <script type="text/javascript" src="{{asset('admin/js/plugins/bootstrap/bootstrap-file-input.js')}}"></script>
        <script type="text/javascript" src="{{asset('admin/js/plugins/bootstrap/bootstrap-select.js')}}"></script>
        <script type="text/javascript" src="{{asset('admin/js/plugins/tagsinput/jquery.tagsinput.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('admin/js/plugins/rangeslider/jQAllRangeSliders-min.js')}}"></script>
        <script type="text/javascript" src="{{asset('admin/js/plugins/ion/ion.rangeSlider.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('admin/js/demo_sliders.js')}}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
        @yield('scripts')
        @stack('custom-scripts')

        <script>
    $(function(){

$(".translate-select li a").click(function(){
     $(".dropdown-translate").text($(this).text());
       var lan = $(this).text();

  $.ajax({
                    url: "{{ route('set.local') }}",
                    type:'GET',
                    data: {lan:lan},
                    dataType: 'json',
                    success: function(data) {
                      /* printMsg(data); */
                      location.reload();
                    }
                });

});

});
    </script>



</body>
</html>

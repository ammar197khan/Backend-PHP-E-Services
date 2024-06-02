<!DOCTYPE html>
<html lang="en">
<head>
    <!-- META SECTION -->
    <title>Qareeb - Company Dashboard</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
    <!-- END PLUGINS -->
    <!-- EOF CSS INCLUDE -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="{{ asset("admin/js/Chart.min.js") }}"></script>
    <script src="{{ asset("admin/js/utils.js") }}"></script>

    <link rel="stylesheet" href="{{ asset('assets/snackbar/snackbar.min.css') }}" media="screen" title="no title" charset="utf-8">
    <script src="{{ asset('assets/snackbar/jquery-2.2.0.min.js') }}" charset="utf-8"></script>
    <script src="{{ asset('assets/snackbar/snackbar.min.js') }}" charset="utf-8"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

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
         @if(\Session::get('current_locale',config('app.fallback_locale','en')) == 'ar')
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
          .panel .panel-default .panel-heading span.btn {
            float: unset!important;
          }
          /* .panel .panel-default, .panel-heading,  a {
            float: left!important;
          } */
          .panel .panel-default .panel-heading  a .btn-success{
            float: left!important;
          }
          button.close {
            float: left!important;
          }
          .panel-footer .btn  .btn-primary{
            float: right!important;
          }
          .form-horizontal .input-group{
            width: 80%!important;
          }
          .form-horizontal .input-group span.input-group-addon{
                   float:unset!important;
                   padding-right: 2px;
          }
          .form-horizontal .input-group span.input-group-addon .btn{
                   float:unset!important;
          }
          tr > td:last-of-type  a button {
             margin-right: 2px;

             }
             div .panel-default .panel-heading a {
            float: right!important;
            margin-right:2px;
          }
          div .panel-default .panel-heading div {
            float: left!important;
          }
          .panel-default .panel-heading a .fa-plus{
            display: none;
          }
          .modal-content .btn-default {
            margin-right: 12px;
            margin-top: -40px;
            float: right !important;
          }
          td div .dropdown-menu-right  {
            right: auto;
            left: 0;
          }
          /* avain td div .dropdown-menu-right li  {
            float: right!important;
          }  */
          /* td div .dropdown-menu > li > a {
            float: unset!important;
        } */
        /* .panel .panel-default, .panel-heading, a {
    float: unset !important;
} */


        @endif
    </style>
</head>
<body>
<!-- START PAGE CONTAINER -->
<div class="page-container page-mode-ltr page-content-ltr">
    <!-- START PAGE SIDEBAR -->
    <div class="page-sidebar page-sidebar-fixed scroll">
        <!-- START X-NAVIGATION -->
        <ul class="x-navigation">
            <li class="xn-logo">
                <a href="/company/dashboard">Qareeb - Company Dashboard</a>
                <a href="#" class="x-navigation-control"></a>
            </li>

            <li class="xn-profile">
                <div class="profile">
                    <div class="profile-image">
                        @php
                        $logo = "";
                        $companyID= company()->company_id;
                        $findLogo = DB::table('companies')->where('id', $companyID)->first();
                        if($findLogo && $findLogo != ""){ $logo = $findLogo->logo; }
                        @endphp
                        <img src="/companies/logos/{{$logo}}" alt="{{company()->en_name}}" style="width: 110px; height: 110px;"/>
                    </div>
                    <div class="profile-controls">
                        <a href="{{route('company.admin.profile.index')}}" class="profile-control-left" title="View Profile"><span class="fa fa-user"></span></a>
                    </div>
                </div>
            </li>

            <li @if(Request::is('company/dashboard')) class="active" @endif>
                <a href="/company/dashboard"><span class="fa fa-dashboard"></span><span class="xn-text">{{ __('language.Dashboard') }}</span></a>
            </li>

            @if(company()->hasPermissionTo('View admin'))
                <li @if(Request::is('company/admins/*')) class="active" @endif>
                    <a href="{{route('company.admins.index')}}"><span class="fa fa-user-secret"></span><span class="xn-text"> {{ __('language.Admins') }}</span></a>
                </li>
            @endif

            @if(company()->hasPermissionTo('View company info'))
                <li @if(Request::is('company/profile*')) class="active" @endif>
                    <a href="{{route('company.profile')}}"><span class="fa fa-info-circle"></span><span class="xn-text">{{ __('language.Company Profile') }}</span></a>
                </li>
            @endif

            {{--<li @if(Request::is('company/info')) class="active" @endif>--}}
                {{--<a href="/company/info"><span class="fa fa-info-circle"></span><span class="xn-text">{{ __('language.Company Info') }}</span></a>--}}
            {{--</li>--}}

            @if(company()->hasPermissionTo('View sub company'))
                <li @if(Request::is('company/sub_companies/*')) class="active" @endif>
                    <a href="/company/sub_companies/active"><span class="fa fa-building"></span><span class="xn-text">{{ __('language.Sub Companies') }}</span></a>
                </li>
            @endif

            @if(company()->hasPermissionTo('View collaboration'))
                <li @if(Request::is('company/collaborations') xor Request::is('company/collaboration/*')) class="active" @endif>
                    <a href="/company/collaborations"><span class="fa fa-handshake-o"></span><span class="xn-text">{{ __('language.Partnership') }}</span></a>
                </li>
            @endif

            @if(company()->hasPermissionTo('View user'))
                <li @if(Request::is('company/users/*')) class="active" @endif>
                    <a href="/company/users/active"><span class="fa fa-user"></span><span class="xn-text">{{ __('language.Users') }}</span></a>
                </li>

                <li @if(Request::is('company/house_types/*')) class="active" @endif>
                    <a href="/company/house_types"><span class="fa fa-home"></span><span class="xn-text">{{ __('language.House Types') }}</span></a>
                </li>
            @endif

            @if(company()->hasPermissionTo('View orders'))
                <li @if(Request::is('company/orders/*')) class="active" @endif>
                    <a href="/company/orders/all"><span class="fa fa-truck"></span><span class="xn-text">{{ __('language.Orders') }}</span></a>
                </li>
            @endif
            @if(company()->hasPermissionTo('SLA Order Dashboard'))
            <li @if(Request::is('company/sla/order-dashboard')) class="active" @endif>
                            <a href="/company/sla/order-dashboard"><span class="fa fa-truck"></span><span class="xn-text">SLA Order Dashboard</span></a>
                        </li>
                        @endif

            {{--<li @if(Request::is('company/bills')) class="active" @endif>--}}
                {{--<a href="/company/bills"><span class="fa fa-info-circle"></span><span class="xn-text">Bills</span></a>--}}
            {{--</li>--}}

            @if(company()->hasPermissionTo('View item request'))
                <li class="xn-openable @if(Request::is('company/show/item_requests/*') xor Request::is('company/show/item_request/*')) active @endif">
                    <a href="#"><span class="fa fa-question-circle-o"></span><span class="xn-text">{{ __('language.Item Requests') }}</span></a>
                    <ul>
                        <li @if(Request::is('company/show/item_requests/awaiting')) class="active" @endif>
                            <a href="/company/show/item_requests/awaiting"><span class="fa fa-check-square"></span><span class="xn-text">{{ __('language.Awaiting') }}</span></a>
                        </li>
                        <li @if(Request::is('company/show/item_requests/confirmed')) class="active" @endif>
                            <a href="/company/show/item_requests/confirmed"><span class="fa fa-clock-o"></span><span class="xn-text">{{ __('language.Confirmed') }}</span></a>
                        </li>
                        <li @if(Request::is('company/show/item_requests/declined')) class="active" @endif>
                            <a href="/company/show/item_requests/declined"><span class="fa fa-minus-circle"></span><span class="xn-text">{{ __('language.Declined') }}</span></a>
                        </li>
                    </ul>
                </li>
            @endif
            @if(company()->hasPermissionTo('Create SLA'))
            <li @if(Request::is('company/SLA/index/*')) class="active" @endif>
                            <a href="/company/SLA/index/"><span class="fa fa-question-circle-o"></span><span class="xn-text">SLA</span></a>
                        </li>
            @endif
            {{-- <li @if(Request::is('company/appliances')) class="active" @endif>
                <a href="/company/appliances"><span class="fa fa-television"></span><span class="xn-text">{{ __('language.Appliances') }}</span></a>
            </li> --}}

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
                            <a href="{{route('company.logout')}}" class="btn btn-success btn-lg">Yes</a>
                            <button class="btn btn-default btn-lg mb-control-close">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END MESSAGE BOX-->

     @yield('content')





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
        <script type="text/javascript">

            $.ajaxSetup({

                headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                     }
                    });
                    function empty( val ) {

// test results
//---------------
// []        true, empty array
// {}        true, empty object
// null      true
// undefined true
// ""        true, empty string
// ''        true, empty string
// 0         false, number
// true      false, boolean
// false     false, boolean
// Date      false
// function  false

    if (val === undefined)
    return true;

if (typeof (val) == 'function' || typeof (val) == 'number' || typeof (val) == 'boolean' || Object.prototype.toString.call(val) === '[object Date]')
    return false;

if (val == null || val.length === 0)        // null or 0 length array
    return true;

if (typeof (val) == "object") {
    // empty object

    var r = true;

    for (var f in val)
        r = false;

    return r;
}

return false;
}

                    function capitalize(str) {
  strVal = '';
  str = str.split(' ');
  for (var chr = 0; chr < str.length; chr++) {
    strVal += str[chr].substring(0, 1).toUpperCase() + str[chr].substring(1, str[chr].length) + ' '
  }
  return strVal
}
</script>
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
    @stack('custom-scripts')
        @yield('scripts')

<!-- END THIS PAGE PLUGINS -->
<!-- END SCRIPTS -->
</body>
</html>

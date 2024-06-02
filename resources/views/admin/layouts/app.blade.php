<!DOCTYPE html>
<html lang="en">
<head>
    <!-- META SECTION -->
    <title>Qareeb - Dashboard</title>
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
        @media print {
            a[href]:after {
                content: none !important;
                text-decoration: none !important;
            }
        }
        </style>
        @if(\Session::get('current_locale',config('app.fallback_locale','en')) == 'ar')
        <style>
        body{
            direction: rtl;
            text-align: right;


        }
        *:not(i,span){
            font-family:DIN!important;
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
             tr > td:last-of-type button {
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
        </style>
        @endif

</head>
<body>
<!-- START PAGE CONTAINER -->
<div class="page-container page-mode-ltr page-content-ltr">
    <!-- START PAGE SIDEBAR -->
    <div class="page-sidebar {{-- page-sidebar-fixed scroll --}}" id="sideMenu">
        <!-- START X-NAVIGATION -->
        <ul class="x-navigation">
            <li class="xn-logo">
                <a href="/admin/dashboard">Qareeb - Super Admin Dashboard</a>
                <a href="#" class="x-navigation-control"></a>
            </li>

            <li class="xn-profile">
                <div class="profile">
                    <div class="profile-image">
                        <img src="/qareeb_admins/{{admin()->image}}" alt="Qareeb" style="width: 110px; height: 110px;"/>
                    </div>
                    <div class="profile-controls">
                        <a href="/admin/profile" class="profile-control-left" title="{{ __('language.View Profile') }}"><span class="fa fa-user"></span></a>
                    </div>
                </div>
            </li>

            <li @if(Request::is('admin/dashboard')) class="active" @endif>
                <a href="/admin/dashboard"><span class="fa fa-dashboard"></span><span class="xn-text">{{ __('language.Dashboard') }}</span></a>
            </li>

            @if(admin()->hasPermissionTo('View admin'))
                <li @if(Request::is('admin/admins/*')) class="active" @endif>
                    <a href="/admin/admins/index"><span class="fa fa-user-secret"></span><span class="xn-text">{{ __('language.Admins') }}</span></a>
                </li>
            @endif

            @if(admin()->hasPermissionTo('View category'))
                <li @if(Request::is('admin/categories/*') xor Request::is('admin/category/*')) class="active" @endif>
                    <a href="/admin/categories"><span class="fa fa-cubes"></span><span class="xn-text">{{ __('language.Categories') }}</span></a>
                </li>
            @endif

            @if(admin()->hasPermissionTo('View provider'))
                <li @if(Request::is('admin/providers')) class="active" @endif>
                    <a href="/admin/providers"><span class="fa fa-industry"></span><span class="xn-text">{{ __('language.Providers') }}</span></a>
                </li>
            @endif

            <li @if(Request::is('admin/provider/bills/all')) class="active" @endif>
              <a href="/admin/provider/bills/all"><span class="fa fa-bitcoin"></span><span class="xn-text">{{ __('language.Bills') }}</span></a>
            </li>

            {{--<li class="xn-openable @if(Request::is('admin/individuals/*') xor Request::is('admin/individual/*')) active @endif" >--}}
                {{--<a href="#"><span class="fa fa-handshake-o"></span><span class="xn-text">Individuals</span></a>--}}
                {{--<ul>--}}
                    {{--<li @if(Request::is('admin/individuals/active')) class="active" @endif>--}}
                        {{--<a href="/admin/individuals/active"><span class="fa fa-check-square"></span><span class="xn-text">Active</span></a>--}}
                    {{--</li>--}}
                    {{--<li @if(Request::is('admin/individuals/suspended')) class="active" @endif>--}}
                        {{--<a href="/admin/individuals/suspended"><span class="fa fa-minus-square"></span><span class="xn-text">Suspended</span></a>--}}
                    {{--</li>--}}
                {{--</ul>--}}
            {{--</li>--}}

            @if(admin()->hasPermissionTo('View company'))
                <li @if(Request::is('admin/companies')) class="active" @endif>
                    <a href="/admin/companies"><span class="fa fa-building"></span><span class="xn-text">{{ __('language.Companies') }}</span></a>
                </li>
            @endif

            <li @if(Request::is('admin/orders/dashboard/*') || Request::is('admin/orders/all/*')) class="active" @endif>
                <a href="/admin/orders/dashboard/all"><span class="fa fa-truck"></span><span class="xn-text">{{ __('language.Orders') }}</span></a>
            </li>

            <li class="xn-openable @if(Request::is('admin/individual/*') xor Request::is('admin/individuals/*')) active @endif" >
                <a href="#"><span class="fa fa-users"></span><span class="xn-text">{{ __('language.Individual') }}</span></a>
                <ul>
                    <li class="xn-openable @if(Request::is('admin/individual/user*') xor Request::is('admin/individuals/user*')) active @endif" >
                        <a href="#"><span class="fa fa-user"></span><span class="xn-text">{{ __('language.Users') }}</span></a>
                        <ul>
                            <li @if(Request::is('admin/individuals/user/active')) class="active" @endif>
                                <a href="/admin/individuals/user/active"><span class="fa fa-check-square"></span><span class="xn-text"> {{ __('language.Active') }}</span></a>
                            </li>
                            <li @if(Request::is('admin/individuals/user/suspended')) class="active" @endif>
                                <a href="/admin/individuals/user/suspended"><span class="fa fa-minus-square"></span><span class="xn-text">{{ __('language.Suspended') }}</span></a>
                            </li>
                        </ul>
                    </li>

                    <li class="xn-openable @if(Request::is('admin/individual/technician*') xor Request::is('admin/individuals/technician*')) active @endif" >
                        <a href="#"><span class="fa fa-wrench"></span><span class="xn-text"> {{ __('language.Technicians') }}</span></a>
                        <ul>
                            <li @if(Request::is('admin/individuals/technician/active')) class="active" @endif>
                                <a href="/admin/individuals/technician/active"><span class="fa fa-check-square"></span><span class="xn-text"> {{ __('language.Active') }}</span></a>
                            </li>
                            <li @if(Request::is('admin/individuals/suspended')) class="active" @endif>
                                <a href="/admin/individuals/technician/suspended"><span class="fa fa-minus-square"></span><span class="xn-text">{{ __('language.Suspended') }}</span></a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </li>

            {{--<li class="xn-openable @if(Request::is('admin/users/*') xor Request::is('admin/user/*')) active @endif" >--}}
                {{--<a href="#"><span class="fa fa-users"></span><span class="xn-text">Users</span></a>--}}
                {{--<ul>--}}
                    {{--<li @if(Request::is('admin/users/active')) class="active" @endif>--}}
                        {{--<a href="/admin/users/active"><span class="fa fa-check-square"></span><span class="xn-text">Active</span></a>--}}
                    {{--</li>--}}
                    {{--<li @if(Request::is('admin/users/suspended')) class="active" @endif>--}}
                        {{--<a href="/admin/users/suspended"><span class="fa fa-minus-square"></span><span class="xn-text">Suspended</span></a>--}}
                    {{--</li>--}}
                {{--</ul>--}}
            {{--</li>--}}

            @if(admin()->hasPermissionTo('View collaboration'))
                <li @if(Request::is('admin/collaborations') xor Request::is('admin/collaboration/*')) class="active" @endif>
                    <a href="/admin/collaborations"><span class="fa fa-handshake-o"></span><span class="xn-text">{{ __('language.Partnership') }}</span></a>
                </li>
            @endif

            @if(admin()->hasPermissionTo('View settings'))
              <li class="xn-openable @if(Request::is('admin/settings/*') || Request::is('admin/addresses/*')) active @endif" onclick="scrollToMenuBottom()">
                    <a href="#"><span class="xn-text"><span class="fa fa-cogs"></span> {{ __('language.Application Settings') }}</span></a>
                    <ul>
                        @if(admin()->hasPermissionTo('View Address'))
                            <li @if(Request::is('admin/addresses/*')) class="active" @endif>
                                <a href="/admin/addresses/all"><span class="fa fa-flag"></span><span class="xn-text">{{ __('language.Addresses') }}</span></a>
                            </li>
                        @endif

                        <li @if(Request::is('admin/settings/about')) class="active" @endif>
                            <a href="/admin/settings/about"><span class="xn-text"><span class="fa fa-info-circle"></span>About Us</span></a>
                        </li>
                        <li @if(Request::is('admin/settings/terms')) class="active" @endif>
                            <a href="/admin/settings/terms"><span class="xn-text"><span class="fa fa-bars"></span>Terms</span></a>
                        </li>
                        <li @if(Request::is('admin/settings/privacy')) class="active" @endif>
                            <a href="/admin/settings/privacy"><span class="xn-text"><span class="fa fa-user-secret"></span>Privacy</span></a>
                        </li>
                        <li @if(Request::is('admin/settings/complains')) class="active" @endif>
                            <a href="/admin/settings/complains"><span class="xn-text"><span class="fa fa-list-alt"></span>Complains</span></a>
                        </li>

                        <div id="endOfSideMenu"></div>

                        {{--<li @if(Request::is('admin/settings/notifications')) class="active" @endif>--}}
                            {{--<a href="/admin/settings/notifications"><span class="xn-text">الإشعارات العامة</span><span class="fa fa-newspaper-o"></span></a>--}}
                        {{--</li>--}}
                    </ul>
                </li>
            @endif
            <script type="text/javascript">
              function scrollToMenuBottom() {
                  var objDiv = document.getElementById("sideMenu");
                  objDiv.scrollTop = objDiv.scrollHeight;
              }
            </script>

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
                            <a href="/admin/logout" class="btn btn-success btn-lg">Yes</a>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
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

@yield('scripts')

<!-- END THIS PAGE PLUGINS -->
<!-- END SCRIPTS -->
</body>
</html>

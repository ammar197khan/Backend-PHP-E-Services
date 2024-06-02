<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>
    Qareeb Admin Dashboard
  </title>
  <!-- Favicon -->
  <link href="{{ asset("assets/img/brand/favicon.png") }}" rel="icon" type="image/png">
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
  <!-- Icons -->
  <link href="{{ asset("assets/js/plugins/nucleo/css/nucleo.css") }}" rel="stylesheet" />
  <link href="{{ asset("assets/js/plugins/@fortawesome/fontawesome-free/css/all.min.css") }}" rel="stylesheet" />
  <!-- CSS Files -->
  <link href="{{ asset("assets/css/argon-dashboard.css?v=1.1.0") }}" rel="stylesheet" />
</head>

<body class="">

  @include('dashboard.layouts.partials.adminSidebar')
  {{-- @include('dashboard.layouts.partials.companySidebar')
  @include('dashboard.layouts.partials.providerSidebar') --}}

  <div class="main-content">
    <!-- Navbar -->
    <nav class="navbar navbar-top navbar-expand-md navbar-dark" id="navbar-main">
      <div class="container-fluid">
        <!-- Brand -->
        <a class="h4 mb-0 text-white text-uppercase d-none d-lg-inline-block" href="./index.html">@yield('title')</a>
        <!-- Form -->
        <form class="navbar-search navbar-search-dark form-inline mr-3 d-none d-md-flex ml-lg-auto">
          <div class="form-group mb-0">
            <div class="input-group input-group-alternative">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
              </div>
              <input class="form-control" placeholder="Search" type="text">
            </div>
          </div>
        </form>
        <!-- User -->
        <ul class="navbar-nav align-items-center d-none d-md-flex">
          <li class="nav-item dropdown">
            <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <div class="media align-items-center">
                <span class="avatar avatar-sm rounded-circle">
                  <img alt="Image placeholder" src="{{ asset("assets/img/theme/team-1-800x800.jpg") }}">
                </span>
                <div class="media-body ml-2 d-none d-lg-block">
                  <span class="mb-0 text-sm  font-weight-bold">Super Admin</span>
                </div>
              </div>
            </a>
            <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
              <div class=" dropdown-header noti-title">
                <h6 class="text-overflow m-0">Welcome!</h6>
              </div>
              <a href="./examples/profile.html" class="dropdown-item">
                <i class="ni ni-single-02"></i>
                <span>My profile</span>
              </a>
              <a href="./examples/profile.html" class="dropdown-item">
                <i class="ni ni-settings-gear-65"></i>
                <span>{{ __('language.Settings') }}</span>
              </a>
              <a href="./examples/profile.html" class="dropdown-item">
                <i class="ni ni-calendar-grid-58"></i>
                <span>Activity</span>
              </a>
              <a href="./examples/profile.html" class="dropdown-item">
                <i class="ni ni-support-16"></i>
                <span>Support</span>
              </a>
              <div class="dropdown-divider"></div>
              <a href="#!" class="dropdown-item">
                <i class="ni ni-user-run"></i>
                <span>Logout</span>
              </a>
            </div>
          </li>
        </ul>
      </div>
    </nav>
    <!-- End Navbar -->
    <!-- Header -->
    <div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
      <div class="container-fluid">
        <div class="header-body">
          @yield('header')
        </div>
      </div>
    </div>

    <div class="container-fluid mt--7">
      @yield('content')
      <!-- Footer -->
      @include('dashboard.layouts.partials.footer')
    </div>
  </div>
  <!--   Core   -->
  <script src="{{ asset("assets/js/plugins/jquery/dist/jquery.min.js") }}"></script>
  <script src="{{ asset("assets/js/plugins/bootstrap/dist/js/bootstrap.bundle.min.js") }}"></script>
  <!--   Optional JS   -->
  <script src="{{ asset("assets/js/plugins/chart.js/dist/Chart.min.js") }}"></script>
  <script src="{{ asset("assets/js/plugins/chart.js/dist/Chart.extension.js") }}"></script>
  <!--   Argon JS   -->
  <script src="{{ asset("assets/js/argon-dashboard.min.js?v=1.1.0") }}"></script>
  <script src="https://cdn.trackjs.com/agent/v3/latest/t.js"></script>
  <script>
    window.TrackJS &&
      TrackJS.install({
        token: "ee6fab19c5a04ac1a32a645abde4613a",
        application: "argon-dashboard-free"
      });
  </script>
</body>

</html>

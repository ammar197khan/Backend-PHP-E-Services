@extends('dashboard.layouts.app')
@section('header')
<div class="row">
    @component('dashboard.layouts.components.card', ['title'=>'MONTHLY TOTAL ORDERS COUNT', 'count'=>'105485', 'icon'=>'shipping-fast', 'iconTheme'=>'info'])
        <span class="text-info mr-2">
          <a href="#">
            <i class="fa fa-external-link-alt"></i> <b>1392 in this year</b>
          </a>
        </span>
    @endcomponent

    @component('dashboard.layouts.components.card', ['title'=>'MONTHLY PENDING ORDERS', 'count'=>'105485', 'icon'=>'folder-open', 'iconTheme'=>'warning'])
        <span class="text-info mr-2">
          <a href="#">
            <i class="fa fa-external-link-alt"></i> <b>1392 in this year</b>
          </a>
        </span>
    @endcomponent

    @component('dashboard.layouts.components.card', ['title'=>'MONTHLY COMPLETED ORDERS', 'count'=>'105485', 'icon'=>'check', 'iconTheme'=>'green'])
        <span class="text-info mr-2">
          <a href="#">
            <i class="fa fa-external-link-alt"></i> <b>1392 in this year</b>
          </a>
        </span>
    @endcomponent

    @component('dashboard.layouts.components.card', ['title'=>'MONTHLY CANCELED ORDERS', 'count'=>'105485', 'icon'=>'times', 'iconTheme'=>'danger'])
        <span class="text-info mr-2">
          <a href="#">
            <i class="fa fa-external-link-alt"></i> <b>1392 in this year</b>
          </a>
        </span>
    @endcomponent
</div>

@endsection


@section('content')
<div class="row">
  <div class="col-xl-8 mb-5 mb-xl-0">
    <div class="card bg-gradient-default shadow">
      <div class="card-header bg-transparent">
        <div class="row align-items-center">
          <div class="col">
            <h6 class="text-uppercase text-light ls-1 mb-1">Overview</h6>
            <h2 class="text-white mb-0">Sales value</h2>
          </div>
          <div class="col">
            <ul class="nav nav-pills justify-content-end">
              <li class="nav-item mr-2 mr-md-0" data-toggle="chart" data-target="#chart-sales" data-update='{"data":{"datasets":[{"data":[0, 20, 10, 30, 15, 40, 20, 60, 60]}]}}' data-prefix="$" data-suffix="k">
                <a href="#" class="nav-link py-2 px-3 active" data-toggle="tab">
                  <span class="d-none d-md-block">Month</span>
                  <span class="d-md-none">M</span>
                </a>
              </li>
              <li class="nav-item" data-toggle="chart" data-target="#chart-sales" data-update='{"data":{"datasets":[{"data":[0, 20, 5, 25, 10, 30, 15, 40, 40]}]}}' data-prefix="$" data-suffix="k">
                <a href="#" class="nav-link py-2 px-3" data-toggle="tab">
                  <span class="d-none d-md-block">Week</span>
                  <span class="d-md-none">W</span>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="card-body">
        <!-- Chart -->
        <div class="chart">
          <!-- Chart wrapper -->
          <canvas id="chart-sales" class="chart-canvas"></canvas>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-4">
    <div class="card shadow">
      <div class="card-header bg-transparent">
        <div class="row align-items-center">
          <div class="col">
            <h6 class="text-uppercase text-muted ls-1 mb-1">Performance</h6>
            <h2 class="mb-0">{{ __('language.Total orders') }}</h2>
          </div>
        </div>
      </div>
      <div class="card-body">
        <!-- Chart -->
        <div class="chart">
          <canvas id="chart-orders" class="chart-canvas"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="row" style="margin-top:30px; padding:25px 0px 25px 0px; background-color:#eff0f4; border-radius: 10px;">
    @component('dashboard.layouts.components.card', ['title'=>'COMMITMENT','count'=>'5/5', 'icon'=>'star', 'iconTheme'=>'yellow'])
        <span class="text-info mr-2">
          <a href="#">
            <i class="fa fa-external-link-alt"></i> <b>reviews</b>
          </a>
        </span>
    @endcomponent

    @component('dashboard.layouts.components.card', ['title'=>'APPEARANCE', 'count'=>'5/5', 'icon'=>'star', 'iconTheme'=>'yellow'])
        <span class="text-info mr-2">
          <a href="#">
            <i class="fa fa-external-link-alt"></i> <b>reviews</b>
          </a>
        </span>
    @endcomponent

    @component('dashboard.layouts.components.card', ['title'=>'PERFORMANCE', 'count'=>'5/5', 'icon'=>'star', 'iconTheme'=>'yellow'])
        <span class="text-info mr-2">
          <a href="#">
            <i class="fa fa-external-link-alt"></i> <b>reviews</b>
          </a>
        </span>
    @endcomponent

    @component('dashboard.layouts.components.card', ['title'=>'CLEANLINESS', 'count'=>'5/5', 'icon'=>'star', 'iconTheme'=>'yellow'])
        <span class="text-info mr-2">
          <a href="#">
            <i class="fa fa-external-link-alt"></i> <b>reviews</b>
          </a>
        </span>
    @endcomponent
</div>

<div class="row mt-5">



  <div class="col-xl-4">
    @component('dashboard.layouts.components.cardTable', ['title'=>'TOP SERVICES'])
        @slot('sideHeader')
            <a href="#!" class="btn btn-sm btn-primary">See all</a>
        @endslot
        <thead class="thead-light">
            <tr>
                <th scope="col">{{ __('language.Service') }}</th>
                <th scope="col">ORDERS</th>
                <th scope="col">REVENUE</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th scope="row">Dish washer</th>
                <td>1,480</td>
                <td>1,480</td>
            </tr>
            <tr>
                <th scope="row">Split type ac</th>
                <td>1,480</td>
                <td>1,480</td>
            </tr>
            <tr>
                <th scope="row">Dryer machine</th>
                <td>1,480</td>
                <td>1,480</td>
            </tr>
            <tr>
                <th scope="row">TV Cable</th>
                <td>1,480</td>
                <td>1,480</td>
            </tr>
        </tbody>
    @endcomponent
  </div>

  <div class="col-xl-4">
    @component('dashboard.layouts.components.cardTable', ['title'=>'TOP PROVIDERS'])
        @slot('sideHeader')
            <a href="#!" class="btn btn-sm btn-primary">See all</a>
        @endslot
        <thead class="thead-light">
            <tr>
                <th scope="col">{{ __('language.Service') }}</th>
                <th scope="col">ORDERS</th>
                <th scope="col">REVENUE</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th scope="row">AL-TAMIMI</th>
                <td>1,480</td>
                <td>1,480</td>
            </tr>
            <tr>
                <th scope="row">Al Tamimi</th>
                <td>1,480</td>
                <td>1,480</td>
            </tr>
            <tr>
                <th scope="row">Mustafa</th>
                <td>1,480</td>
                <td>1,480</td>
            </tr>
            <tr>
                <th scope="row">pro44</th>
                <td>1,480</td>
                <td>1,480</td>
            </tr>
        </tbody>
    @endcomponent
  </div>

  <div class="col-xl-4">
    @component('dashboard.layouts.components.cardTable', ['title'=>'TOP TECHNECIANS'])
        @slot('sideHeader')
            <a href="#!" class="btn btn-sm btn-primary">See all</a>
        @endslot
        <thead class="thead-light">
            <tr>
                <th scope="col">{{ __('language.Service') }}</th>
                <th scope="col">ORDERS</th>
                <th scope="col">REVENUE</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th scope="row">tec44</th>
                <td>1,480</td>
                <td>1,480</td>
            </tr>
            <tr>
                <th scope="row">Mustafa</th>
                <td>1,480</td>
                <td>1,480</td>
            </tr>
            <tr>
                <th scope="row">ASHFAQ AMHED KAYAN</th>
                <td>1,480</td>
                <td>1,480</td>
            </tr>
            <tr>
                <th scope="row">ABHILASH CHERAN</th>
                <td>1,480</td>
                <td>1,480</td>
            </tr>
        </tbody>
    @endcomponent
  </div>



</div>
@endsection

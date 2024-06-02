@php
$explode = explode('/',$_SERVER['REQUEST_URI']);
$dashboard_name = $explode[1];
@endphp
<style>
    div.datepicker {
 min-width: 196px;
    }

 </style>
<div class="page-content-wrap" style="margin-top: 10px;">
    <div class="row">
          <div class="col-md-3">
              @component('admin.home.components.widget', ['icon' => 'truck'])
                  {{ __('language.Monthly Orders') }}
                  @slot('count')
                      @if(strpos($_SERVER['REQUEST_URI'],'admin') && strpos($_SERVER['REQUEST_URI'],'statistics'))
                          <a href="/{{$explode[1]}}/{{$explode[2]}}/{{$explode[3]}}/{{$explode[4]}}/monthly_orders_count" style="color: #337ab7">{{$data['monthly_orders_count']}}</a>
                      @elseif( strpos($_SERVER['REQUEST_URI'],'statistics'))
                          <a href="statistics/monthly_orders_count" style="color: #337ab7">{{$data['monthly_orders_count']}}</a>
                      @else
                          <a href="/{{$dashboard_name}}/orders/dashboard/monthly_orders_count" style="color: #337ab7">{{$data['monthly_orders_count']}}</a>
                      @endif
                  @endslot
                  @slot('count_year')
                      @if(strpos($_SERVER['REQUEST_URI'],'admin') && strpos($_SERVER['REQUEST_URI'],'statistics'))
                          <a href="/{{$explode[1]}}/{{$explode[2]}}/{{$explode[3]}}/{{$explode[4]}}/yearly_orders_count" style="color: #337ab7">{{$data['yearly_orders_count']}}</a>
                      @elseif( strpos($_SERVER['REQUEST_URI'],'statistics'))
                          <a href="statistics/yearly_orders_count" style="color: #337ab7">{{$data['yearly_orders_count']}}</a>
                      @else
                          <a href="/{{$dashboard_name}}/orders/dashboard/yearly_orders_count" style="color: #337ab7">{{$data['yearly_orders_count']}}</a>
                      @endif
                  @endslot
                  @slot('subTitle')
                      {{ __('language.YTD') }}
                  @endslot
              @endcomponent
          </div>
          <div class="col-md-3">
              @component('admin.home.components.widget', ['icon' => 'folder-open'])
              {{ __('language.Monthly Open Orders') }}
                  @slot('count')
                      @if(strpos($_SERVER['REQUEST_URI'],'admin') && strpos($_SERVER['REQUEST_URI'],'statistics'))
                          <a href="/{{$explode[1]}}/{{$explode[2]}}/{{$explode[3]}}/{{$explode[4]}}/monthly_open" style="color: orange">{{$data['monthly_open']}}</a>
                      @elseif( strpos($_SERVER['REQUEST_URI'],'statistics'))
                          <a href="statistics/monthly_open" style="color: orange">{{$data['monthly_open']}}</a>
                      @else
                          <a href="/{{$dashboard_name}}/orders/dashboard/monthly_open" style="color: orange">{{$data['monthly_open']}}</a>
                      @endif
                  @endslot
                  @slot('count_year')
                      @if(strpos($_SERVER['REQUEST_URI'],'admin') && strpos($_SERVER['REQUEST_URI'],'statistics'))
                          <a href="/{{$explode[1]}}/{{$explode[2]}}/{{$explode[3]}}/{{$explode[4]}}/yearly_open" style="color: orange">{{$data['yearly_open']}}</a>
                      @elseif( strpos($_SERVER['REQUEST_URI'],'statistics'))
                          <a href="statistics/yearly_open" style="color: orange">{{$data['yearly_open']}}</a>
                      @else
                        <a href="/{{$dashboard_name}}/orders/dashboard/yearly_open" style="color: orange">{{$data['yearly_open']}}</a>
                      @endif
                  @endslot
                  @slot('subTitle')
                      {{ __('language.YTD') }}
                  @endslot

              @endcomponent
          </div>
          <div class="col-md-3">
              @component('admin.home.components.widget', ['icon' => 'check-circle'])
                  {{ __('language.Monthly Finished Orders') }}
                  @slot('count')
                      @if(strpos($_SERVER['REQUEST_URI'],'admin') && strpos($_SERVER['REQUEST_URI'],'statistics'))
                          <a href="/{{$explode[1]}}/{{$explode[2]}}/{{$explode[3]}}/{{$explode[4]}}/monthly_closed" class="text-success">{{$data['monthly_closed']}}</a>
                      @elseif( strpos($_SERVER['REQUEST_URI'],'statistics'))
                          <a href="statistics/monthly_closed" class="text-success">{{$data['monthly_closed']}}</a>
                      @else
                          <a href="/{{$dashboard_name}}/orders/dashboard/monthly_closed" class="text-success">{{$data['monthly_closed']}}</a>
                      @endif
                  @endslot
                  @slot('count_year')
                      @if(strpos($_SERVER['REQUEST_URI'],'admin') && strpos($_SERVER['REQUEST_URI'],'statistics'))
                          <a href="/{{$explode[1]}}/{{$explode[2]}}/{{$explode[3]}}/{{$explode[4]}}/yearly_closed" class="text-success">{{$data['yearly_closed']}}</a>
                      @elseif( strpos($_SERVER['REQUEST_URI'],'statistics'))
                          <a href="statistics/yearly_closed" class="text-success">{{$data['yearly_closed']}}</a>
                      @else
                          <a href="/{{$dashboard_name}}/orders/dashboard/yearly_closed" class="text-success">{{$data['yearly_closed']}}</a>
                      @endif
                  @endslot
                  @slot('subTitle')
                      {{ __('language.YTD') }}
                  @endslot
              @endcomponent
          </div>
          <div class="col-md-3">
              @component('admin.home.components.widget', ['icon' => 'times-circle'])
              {{ __('language.Monthly Canceled Orders') }}
                  @slot('count')
                      @if(strpos($_SERVER['REQUEST_URI'],'admin') && strpos($_SERVER['REQUEST_URI'],'statistics'))
                          <a href="/{{$explode[1]}}/{{$explode[2]}}/{{$explode[3]}}/{{$explode[4]}}/monthly_canceled" style="color: red">{{$data['monthly_canceled']}}</a>
                      @elseif( strpos($_SERVER['REQUEST_URI'],'statistics'))
                          <a href="statistics/monthly_canceled" style="color: red">{{$data['monthly_canceled']}}</a>
                      @else
                          <a href="/{{$dashboard_name}}/orders/dashboard/monthly_canceled" style="color: red">{{$data['monthly_canceled']}}</a>
                      @endif
                  @endslot
                  @slot('count_year')
                      @if(strpos($_SERVER['REQUEST_URI'],'admin') && strpos($_SERVER['REQUEST_URI'],'statistics'))
                          <a href="/{{$explode[1]}}/{{$explode[2]}}/{{$explode[3]}}/{{$explode[4]}}/yearly_canceled" style="color: red">{{$data['yearly_canceled']}}</a>
                      @elseif( strpos($_SERVER['REQUEST_URI'],'statistics'))
                          <a href="statistics/yearly_canceled" style="color: red">{{$data['yearly_canceled']}}</a>
                      @else
                          <a href="/{{$dashboard_name}}/orders/dashboard/yearly_canceled" style="color: red">{{$data['yearly_canceled']}}</a>
                      @endif
                  @endslot
                  @slot('subTitle')
                      {{ __('language.YTD') }}
                  @endslot
              @endcomponent
          </div>
    </div>
    @php
    $url = '';
      if(strpos($_SERVER['REQUEST_URI'],'company/dashboard') ){
        $url = '/company/dashboard';
      }elseif(strpos($_SERVER['REQUEST_URI'],'provider/dashboard')){
        $url = '/provider/dashboard';
      }
    @endphp
    @php
    if(strpos($_SERVER['REQUEST_URI'],'admin/dashboard') ){

    }else{
        @endphp

        <form class="form-horizontal" method="get" action="{{  $url }}">
<div class="row">

    @php
      if(strpos($_SERVER['REQUEST_URI'],'company/dashboard') ){
        @endphp
        <div class="form-row">
        <div class="col-md-2 mb-3 {{ $errors->has('from') ? ' has-error' : '' }}">
            <label class="control-label">{{ __('language.Date From') }} </label>
            <input  class="form-control datepicker" id="startDate" name="from" placeholder="DD/MM/YYYY" value="{{request('from')}}">
            @include('admin.layouts.error', ['input' => 'from'])
        </div>

        <div class="col-md-2 mb-3 {{ $errors->has('to') ? ' has-error' : '' }}">
            <label class="control-label">{{ __('language.Date To') }} </label>
            <input  name="to" id="endDate" class="form-control datepicker" placeholder="DD/MM/YYYY"   value="{{request('to')}}">
            @include('admin.layouts.error', ['input' => 'to'])
        </div>
    </div>
        @if(isset($providers))
        <div class="col-md-2 mb-3" >
            <label class="control-label">  {{ __('language.Provider') }} </label>
            <select class="form-control select" name="provider_name[]" title="Select A Provider" multiple>
                {{-- <option selected disabled>Select A Provider</option> --}}
                @foreach($providers as $provider)
                    <option value="{{$provider->id}}" @if(request('provider_name')){{in_array($provider->id,request('provider_name')) ? 'selected' : ''}}@endif>{{$provider->en_name}}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="form-row">
            <div class="col-md-2 mb-3" >
              <label class="control-label"> {{ __('language.Sub Company') }}</label>
              <select class="form-control select" id="sub_company" name="sub_company[]" title="Select Company first" multiple>
                @foreach($sub_company as $company)
                <option value="{{$company->id}}" @if(request('sub_company')){{in_array($company->id,request('sub_company')) ? 'selected' : ''}}@endif>{{$company->en_name}}</option>
            @endforeach
              </select>
            </div>
        </div>

        <div class="form-row">
        <div class="col-md-2 mb-3" style="margin-top: 31px;">

        <button type="submit" class="btn btn-info"> <i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
        <a href="{{  Request::url() }}"  class="btn btn-primary">Reset</a>

    </div>

        </div>
        @php
      }elseif(strpos($_SERVER['REQUEST_URI'],'provider/dashboard')){
        @endphp
        <div class="form-row">
        <div class="col-md-2 mb-3 {{ $errors->has('from') ? ' has-error' : '' }}">
            <label class="control-label">{{ __('language.Date From') }} </label>
            <input  class="form-control datepicker" id="startDate" name="from" placeholder="DD/MM/YYYY" value="{{request('from')}}">
            @include('admin.layouts.error', ['input' => 'from'])
        </div>

        <div class="col-md-2 mb-3 {{ $errors->has('to') ? ' has-error' : '' }}">
            <label class="control-label">{{ __('language.Date To') }} </label>
            <input  name="to" id="endDate" class="form-control datepicker" placeholder="DD/MM/YYYY"   value="{{request('to')}}">
            @include('admin.layouts.error', ['input' => 'to'])
        </div>
    </div>
        @if(isset($companies))
        <div class="col-md-2 mb-3">
            <label class="control-label">{{ __('language.Company') }} </label>
            <select class="form-control select" id="company_id" name="company_id[]" title="Select Company" multiple>
              {{-- <option selected disabled>{{ __('language.Select Company') }}</option> --}}
              @foreach($companies as $company)
                <option value="{{$company->id}}" @if(request('company_id')){{in_array($company->id,request('company_id')) ? 'selected' : ''}}@endif>{{$company->en_name}}</option>
              @endforeach
            </select>
        </div>

          <div class="col-md-2 mb-3">
            <label class="control-label">{{ __('language.Sub Company') }} </label>
            <select class="form-control select" id="sub_company" name="sub_company[]" title="Select Company first" multiple>
              {{-- <option selected disabled> {{ __('language.Select Company first') }}</option> --}}
              @foreach($sub_companies as $sub_company)
                <option value="{{$sub_company->id}}" @if(request('sub_company')){{in_array($sub_company->id,request('sub_company')) ? 'selected' : ''}}@endif>{{$sub_company->en_name}}</option>
              @endforeach
            </select>
          </div>
        @endif


        <div class="form-row">
        <div class="col-md-2 mb-3" style="margin-top: 31px;">

        <button type="submit" class="btn btn-info"> <i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
        <a href="{{  Request::url() }}"  class="btn btn-primary">Reset</a>

    </div>

        </div>
        @php
      }
    @endphp


</div>
    </form>
        @php
    }
    @endphp

    <div class="row">

        <div class="col-md-12">
            <a href="#" class="active" style="text-decoration:none; margin:10px" onclick="showCashChart(this)">
                <i class="fa fa-money" aria-hidden="true"></i> {{ __('language.Cash') }}
            </a>
            <a href="#" style="text-decoration:none; margin:10px" onclick="showCountChart(this)">
                <i class="fa fa-list-ol" aria-hidden="true"></i>  {{ __('language.Count') }}
            </a>
        </div>
        <div class="col-md-6">
            @include('admin.home.components.bar_charts', ['data' => $data['monthly_revenue'] ])
        </div>
        <div class="col-md-6">
            @include('admin.home.components.daily_revenue', ['data' => $data['daily_revenue'] ])
        </div>
        @if(isset($data['monthly_revenue_count']) && isset($data['daily_revenue_count']))
            <script type="text/javascript">
            function showCountChart(link) {
                event.preventDefault();
                console.log(window.myBar.options.scales.yAxes[0].scaleLabel.labelString);
                window.myBar.options.scales.yAxes[0].scaleLabel.labelString = "Sales(55)"
                window.myBar.data.datasets[0].data =  {!! json_encode($data['monthly_revenue_count'][1]) !!};
                window.myBar.update();
                window.chart.data.datasets[0].data =  {!! json_encode($data['daily_revenue_count']) !!};
                window.chart.update();
            }
            function showCashChart(link) {
                event.preventDefault();
                window.myBar.data.datasets[0].data =  {!! json_encode($data['monthly_revenue'][1]) !!};
                window.myBar.update();
                window.chart.data.datasets[0].data =  {!! json_encode($data['daily_revenue']) !!};
                window.chart.update();
            }
            </script>
        @endif
    </div>

    <div class="row">
        {{-- <div class="col-md-3">
            @component('admin.home.components.widget', ['icon' => 'mail-reply'])
                Monthly Orders With Spare parts
                @slot('count')
                    <a href="orders/monthly_parts_orders_count">0</a>
                @endslot
                @slot('subTitle')
                    <a href="orders/yearly_parts_orders_count">68</a> In this year
                @endslot
            @endcomponent
        </div>
        <div class="col-md-3">
            @component('admin.home.components.widget', ['icon' => 'cubes'])
                Monthly Spare Parts Requested
                @slot('count')
                    <a href="orders/monthly_parts_orders_count">0</a>
                @endslot
                @slot('subTitle')
                    Out of <a href="items/dashboard/yearly_parts_count">83</a> In this year
                @endslot
            @endcomponent
        </div>

        <div class="col-md-3">
            @component('admin.home.components.widget', ['icon' => 'money'])
                Monthly Spare Parts Total Price
                @slot('count')
                    <a href="price/dashboard/monthly_parts_prices">0 S.R</a>
                @endslot
                @slot('subTitle')
                    Out of <a href="price/dashboard/yearly_parts_prices">73785</a> S.R In this year
                @endslot
            @endcomponent
        </div>
        <div class="col-md-3">
            @component('admin.home.components.widget', ['icon' => 'money'])
                Monthly Orders Revenues
                @slot('count')
                    <a href="price/dashboard/monthly_revenue">50</a>
                @endslot
                @slot('subTitle')
                  Out of <a href="price/dashboard/yearly_revenue">534410</a> In this year
                @endslot
            @endcomponent
        </div> --}}

{{--         @if(isset($data['monthly_parts_orders_count']) || isset($data['yearly_parts_orders_count']))--}}
{{--  1111111111111111
<div class="col-md-3">--}}
{{--            @component('admin.home.components.widget', ['icon' => 'mail-reply'])--}}
{{--                Monthly Spare parts--}}
{{--                @slot('count')--}}
{{--                    <a href="orders/monthly_parts_orders_count" style="color: #337ab7">{{$data['monthly_parts_orders_count']}}</a>--}}
{{--                @endslot--}}
{{--                @slot('count_year')--}}
{{--                    <a href="orders/yearly_parts_orders_count" style="color: #337ab7">{{$data['yearly_parts_orders_count']}}</a>--}}
{{--                @endslot--}}
{{--                @slot('subTitle')--}}
{{--                    YTD--}}
{{--                @endslot--}}
{{--            @endcomponent--}}
{{--        </div>--}}

{{--        <div class="col-md-3">--}}
{{--            @component('admin.home.components.widget', ['icon' => 'cubes'])--}}
{{--                Monthly Requested--}}
{{--                @slot('count')--}}
{{--                    <a href="items/dashboard/monthly_parts_count" style="color: #337ab7">{{$data['monthly_parts_count']}}</a>--}}
{{--                @endslot--}}
{{--                @slot('count_year')--}}
{{--                    <a href="items/dashboard/yearly_parts_count" style="color: #337ab7">{{$data['yearly_parts_count']}}</a>--}}
{{--                @endslot--}}
{{--                @slot('subTitle')--}}
{{--                    YTD--}}
{{--                @endslot--}}
{{--            @endcomponent--}}
{{--        </div>--}}

{{--        <div class="col-md-3">--}}
{{--            @component('admin.home.components.widget', ['icon' => 'money'])--}}
{{--                Monthly Total Price--}}
{{--                @slot('count')--}}
{{--                    <a href="price/dashboard/monthly_parts_prices" style="color: #337ab7">{{$data['monthly_parts_prices']}}</a>--}}
{{--                @endslot--}}
{{--                @slot('count_year')--}}
{{--                    <a href="price/dashboard/yearly_parts_prices" style="color: #337ab7">{{$data['yearly_parts_prices']}}</a>--}}
{{--                @endslot--}}
{{--                @slot('subTitle')--}}
{{--                    YTD--}}
{{--                @endslot--}}
{{--            @endcomponent--}}
{{--        </div>--}}

{{--        <div class="col-md-3">--}}
{{--            @component('admin.home.components.widget', ['icon' => 'money'])--}}
{{--                Monthly Revenues--}}
{{--                @slot('count')--}}
{{--                    <a href="price/dashboard/monthly_revenue" style="color: #337ab7">{{$data['monthly_revenue_widget']}}</a>--}}
{{--                @endslot--}}
{{--                @slot('count_year')--}}
{{--                    <a href="price/dashboard/yearly_revenue" style="color: #337ab7">{{$data['yearly_revenue_widget']}}</a>--}}
{{--                @endslot--}}
{{--                @slot('subTitle')--}}
{{--                    YTD--}}
{{--                @endslot--}}
{{--            @endcomponent--}}
{{--        </div>--}}
{{--        @endif--}}

        @if(Route::currentRouteName() == 'admin.home' || Route::currentRouteName() == 'provider.home')
        <div class="col-md-3">
            @component('admin.home.components.widget_rate', ['icon' => 'thumbs-up'])
            {{ __('language.Commitment') }}
                @slot('count')
                    {{ $data['monthly_rate_commitment'] ?? 0 }}
                @endslot
                @slot('count_month_orders')
                    {{ $data['monthly_rate_count'] ?? 0 }}
                @endslot
                @slot('count_year')
                    {{ $data['yearly_rate_commitment'] ?? 0 }}
                @endslot
                @slot('count_year_orders')
                    {{ $data['yearly_rate_count'] ?? 0 }}
                @endslot
                @slot('subTitle')
                    {{ __('language.YTD') }}
                @endslot
            @endcomponent
        </div>
        <div class="col-md-3">
            @component('admin.home.components.widget_rate', ['icon' => 'thumbs-up'])
            {{ __('language.Appearance') }}
                @slot('count')
                    {{ $data['monthly_rate_appearance'] ?? 0 }}
                @endslot
                @slot('count_month_orders')
                    {{ $data['monthly_rate_count'] ?? 0 }}
                @endslot
                @slot('count_year')
                    {{ $data['yearly_rate_appearance'] ?? 0 }}
                @endslot
                @slot('count_year_orders')
                    {{ $data['yearly_rate_count'] ?? 0 }}
                @endslot
                @slot('subTitle')
                    {{ __('language.YTD') }}
                @endslot
            @endcomponent
        </div>
        <div class="col-md-3">
            @component('admin.home.components.widget_rate', ['icon' => 'thumbs-up'])
            {{ __('language.Performance') }}
                @slot('count')
                    {{ $data['monthly_rate_performance'] ?? 0 }}
                @endslot
                @slot('count_month_orders')
                    {{ $data['monthly_rate_count'] ?? 0 }}
                @endslot
                @slot('count_year')
                    {{ $data['yearly_rate_performance'] ?? 0 }}
                @endslot
                @slot('count_year_orders')
                    {{ $data['yearly_rate_count'] ?? 0 }}
                @endslot
                @slot('subTitle')
                    YTD
                @endslot
            @endcomponent
        </div>
        <div class="col-md-3">
            @component('admin.home.components.widget_rate', ['icon' => 'thumbs-up'])
            {{ __('language.Cleanliness') }}
                @slot('count')
                    {{ $data['monthly_rate_cleanliness'] ?? 0 }}
                @endslot
                @slot('count_month_orders')
                    {{ $data['monthly_rate_count'] ?? 0 }}
                @endslot
                @slot('count_year')
                   {{ $data['yearly_rate_cleanliness'] ?? 0 }}
                @endslot
                @slot('count_year_orders')
                    {{ $data['yearly_rate_count'] ?? 0 }}
                @endslot
                @slot('subTitle')
                    YTD
                @endslot
            @endcomponent
        </div>
        @endif
    </div>

    <div class="row">
        @php
          // $currentPath = request()->path();
          // $adminScope = explode('/', $currentPath)[0];
          // $servicesLink    = $adminScope == 'admin' ? url('admin/categories') : null;
          // $providersLink   = $adminScope == 'admin' ? url('admin/providers') : null;
          // $companiesLink   = $adminScope == 'admin' ? url('admin/companies') : null;
          // $techniciansLink = $adminScope == 'admin' ? url('admin/technicians') : null;
          $servicesLink    = Route::currentRouteName() == 'admin.home' ? url('admin/categories') :
          ( Route::currentRouteName() == 'provider.home' ? url('provider/categories') :
          (Route::currentRouteName() == 'company.home' ? url('company/categories') : null) );
          $providersLink   = Route::currentRouteName() == 'admin.home' ? url('admin/providers') : null;
          $companiesLink   = Route::currentRouteName() == 'admin.home' ? url('admin/companies') :
          (Route::currentRouteName() == 'provider.home' ? url('provider/collaborations') : null);

          if( Route::currentRouteName() == 'admin.home') $techniciansLink =  url('/admin/provider/null/technicians');
          else if(Route::currentRouteName() == 'provider.home') $techniciansLink = url('provider/technicians/active');
          else $techniciansLink = null;
          $usersLink       = Route::currentRouteName() == 'company.home' ? url('company/users/active') : null;
          $subCompaniesLink= Route::currentRouteName() == 'company.home' ? url('company/sub_companies/active') : null;

        @endphp


        <div class="col-md-3">
          @include('admin.home.components.top', ['title'=> __('language.Services') , 'data'=>$data['top_services'],'data_desc' => $data['least_services'], 'link' => $servicesLink])
        </div>
        @if(isset($data['top_providers']))
        <div class="col-md-3">
          @include('admin.home.components.top', ['title'=> __('language.Providers'), 'data'=>$data['top_providers'],'data_desc' => $data['least_providers'], 'link' => $providersLink])
        </div>
        @endif
        @if(isset($data['top_companies']))
            <div class="col-md-3">
                @include('admin.home.components.top', ['title'=> __('language.Companies'), 'data'=>$data['top_companies'],'data_desc' => $data['least_companies'], 'link' => $companiesLink])
            </div>
        @endif
        @if(isset($data['top_users']))
            <div class="col-md-3">
                @include('admin.home.components.top', ['title'=> __('language.Users'), 'data'=>$data['top_users'],'data_desc' => $data['least_users'],'link' => $usersLink])
            </div>
        @endif
        @if(isset($data['top_sub_companies']))
        <div class="col-md-3">
          @include('admin.home.components.top', ['title'=>  __('language.Sub Companies'), 'data'=>$data['top_sub_companies'],'data_desc' => $data['least_sub_companies'],'link' => $subCompaniesLink])
        </div>
        @endif
        @if(isset($data['top_techs']))
        <div class="col-md-3">
          @include('admin.home.components.top', ['title'=> __('language.Sub Technicians'), 'data'=>$data['top_techs'],'data_desc' => $data['least_techs'], 'link' => $techniciansLink])
        </div>
        @endif
        @if(isset($data['top_items']))
        <div class="col-md-3">
          @include('admin.home.components.top', ['title'=> __('language.Items'), 'data'=>$data['top_items'],'data_desc' => $data['least_items']])
        </div>
        @endif
    </div>
{{--    <div class="row">--}}
{{--        <div class="col-md-3">--}}
{{--          @include('admin.home.components.top', ['title'=>'Least Services', 'data'=>$data['least_services'] ])--}}
{{--        </div>--}}
{{--        <div class="col-md-3">--}}
{{--          @include('admin.home.components.top', ['title'=>'Least Providers', 'data'=>$data['least_providers']])--}}
{{--        </div>--}}
{{--        <div class="col-md-3">--}}
{{--          @include('admin.home.components.top', ['title'=>'Least Companies', 'data'=>$data['least_companies']])--}}
{{--        </div>--}}
{{--        <div class="col-md-3">--}}
{{--          @include('admin.home.components.top', ['title'=>'Least Technicians', 'data'=>$data['least_techs']])--}}
{{--        </div>--}}
{{--    </div>--}}

</div>
<script>

    $(function () {

                $("#startDate").datepicker({
                    // autoclose: true,
                    format: 'dd/mm/yyyy',
                    todayHighlight:true,
                }).on('changeDate', function (selected) {
                    var minDate = new Date(selected.date);
                    minDate.setDate(minDate.getDate() + 1);
                    $('#endDate').datepicker('setStartDate', minDate);
                }).next('button').button({
                    icons: {
                        primary: 'fa fa-calendar'
                    }, text:false
                });

                $("#endDate").datepicker({
                    // autoclose: true,
                    format: 'dd/mm/yyyy',
                    todayHighlight:true,
                }).on('changeDate', function (selected) {
                    var minDate = new Date(selected.date);
                    minDate.setDate(minDate.getDate() - 1);
                    $('#startDate').datepicker('setEndDate', minDate);
                }).next('button').button({
                    icons: {
                        primary: 'fa fa-calendar'
                    }, text:false
                });
            });


     $('#company_id').on('change', function (e) {
            var parent_id = $('#company_id').val();
            if (parent_id) {
                $.ajax({
                    url: '/provider/get_sub_company/'+parent_id,
                    type: "GET",

                    dataType: "json",

                    success: function (data) {
                        $('#sub_company').empty();
                        // $('#sub_company').append('<option selected disabled> Select a Sub Company </option>');
                        $.each(data, function (i, sub_company) {
                            var selected = [{{  implode(',',request('sub_company') ? request('sub_company') : []) }}];
                            var includes = selected.includes(sub_company.id);

                            if(includes == true)
                                $('#sub_company').append('<option value="' + sub_company.id + '" selected>' + sub_company.en_name + '</option>');
                            else
                                $('#sub_company').append('<option value="' + sub_company.id + '">' + sub_company.en_name + '</option>');
                        });
                        $('.select').selectpicker('refresh');
                    }
                });

            }
        });
</script>

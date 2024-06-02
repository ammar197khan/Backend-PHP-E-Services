
<div class="form-group">
    <div class="col-md-6 col-xs-12">
        <div class="input-group" style="margin-top: 10px;">
            <input type="text" class="form-control" name="search" value="{{isset($search) ? $search : ''}}" placeholder="Search by SMO No. or Order No. or Technician name or User name" style="margin-top: 1px;"/>
            <span class="input-group-addon btn btn-default">
                <button class="btn btn-default">{{ __('language.Search now') }}</button>
                {{-- <a href="{{  Request::url() }}"  class="btn btn-primary">Reset</a> --}}

                <a class="btn btn-default" data-toggle="collapse" data-target="#advanced_search">
                  <i class="fa fa-sliders"></i>
                  {{ __('language.Advanced Search') }}
                </a>
            </span>
        </div>
    </div>
</div>
<div id="advanced_search" @if(strpos($_SERVER['REQUEST_URI'],'search') !== false)  @else class="collapse card-body" @endif>

    <div class="form-row">
        <div class="col-md-2 mb-3 {{ $errors->has('from') ? ' has-error' : '' }}">
            <label class="control-label"> {{ __('language.Date From') }} </label>
            <input type="date" class="form-control" name="from" value="{{request('from')}}">
            @include('admin.layouts.error', ['input' => 'from'])
        </div>

        <div class="col-md-2 mb-3 {{ $errors->has('to') ? ' has-error' : '' }}">
            <label class="control-label"> {{ __('language.Date To') }}</label>
            <input type="date" class="form-control" name="to" value="{{request('to')}}">
            @include('admin.layouts.error', ['input' => 'to'])
        </div>

    </div>

    <div class="form-row">
        {{-- <div class="col-md-2 mb-3">
            <label class="control-label"> {{ __('language.Service Type') }}</label>
            <select class="form-control select" name="service_type[]" multiple>
                <option selected disabled>{{ __('language.Select service type') }}</option>
                <option value="1" @if(request('service_type')){{in_array(1,request('service_type')) ? 'selected' : ''}}@endif>Preview</option>
                <option value="2" @if(request('service_type')){{in_array(2,request('service_type')) ? 'selected' : ''}}@endif>Maintenance</option>
                <option value="3" @if(request('service_type')){{in_array(3,request('service_type')) ? 'selected' : ''}}@endif>Structure</option>
            </select>
        </div> --}}

        <div class="col-md-2 mb-3">
            <label class="control-label">{{ __('language.Orders Type') }} </label>
            <select class="form-control select" name="order_type[]" title="Select order type" multiple>
                {{-- <option selected disabled>{{ __('language.Select order type') }}</option> --}}
                <option value="urgent" @if(request('order_type')){{in_array('urgent',request('order_type')) ? 'selected' : ''}}@endif>Urgent</option>
                <option value="scheduled" @if(request('order_type')){{in_array('scheduled',request('order_type')) ? 'selected' : ''}}@endif>Scheduled</option>
                <option value="re_scheduled" @if(request('order_type')){{in_array('re_scheduled',request('order_type')) ? 'selected' : ''}}@endif>Re_scheduled</option>
                <option value="emergency" @if(request('order_type')){{in_array('emergency',request('order_type')) ? 'selected' : ''}}@endif>Emergency</option>
            </select>
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
              @php
              $arr_company_parent = request('company_id');
              $sub_company = '';
              if(!empty($arr_company_parent)){
              $sub_company = \App\Models\SubCompany::whereIn('parent_id', $arr_company_parent)->where('status', 'active')->select('id', 'en_name')->get();
              }
              @endphp
              @if(!empty($sub_company))
              @foreach ($sub_company as  $sub_com)
              <option value="{{$sub_com->id}}" @if(request('sub_company')){{in_array($sub_com->id,request('sub_company')) ? 'selected' : ''}}@endif>{{$sub_com->en_name}}</option>
              @endforeach
              @endif
            </select>
          </div>
        @endif
    </div>
    <div class="form-row">

      <div class="col-md-2 mb-3" @if(isset($companies))  @endif>
        <label class="control-label">‫‪‬‬ {{ __('language.Main Category') }} ‬‬ </label>
        <select class="form-control select" id="main_cats" name="main_cats[]" title="Select A Main Categor" multiple>
          {{-- <option selected disabled>{{ __('language.Select A Main Category') }}</option> --}}
          @foreach($cats as $cat)
            <option value="{{$cat->id}}" @if(request('main_cats')){{in_array($cat->id,request('main_cats')) ? 'selected' : ''}}@endif>{{$cat->en_name}}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-2 mb-3" @if(isset($companies))  style="padding-top: 15px" @endif>
        <label class="control-label">‫‪‬‬ ‫‪ {{ __('language.Sub Category') }}‬‬ </label>
        <select class="form-control select" id="sub_cats" name="sub_cats[]" title="Select A Category First" multiple>
          {{-- <option selected disabled>{{ __('language.Select A Category First') }}</option> --}}
          @php
                $arr_parent_category = request('main_cats');
                $sub_cats = '';
                if(!empty( $arr_parent_category)){
                    $sub_cats = \App\Models\Category::whereIn('parent_id', $arr_parent_category)/*->whereIn('id', $subs)*/->with('parent')->get();
                }
          @endphp
          @if(!empty($sub_cats))
          @foreach ($sub_cats as  $sub_cat)
          <option value="{{$sub_cat->id}}" @if(request('sub_company')){{in_array($sub_cat->id,request('sub_cats')) ? 'selected' : ''}}@endif>{{$sub_cat->en_name}}</option>
          @endforeach
          @endif
        </select>
      </div>

      <div class="col-md-2 mb-3" style="padding-top: 15px">
        <label class="control-label"> {{ __('language.Technicians Jobs') }}‬‬ </label>
        <select class="form-control select" id="third_cats" name="third_cats[]" title="Select A Sub Category First" multiple>
          {{-- <option selected disabled>{{ __('language.Select A Sub Category First') }}</option> --}}
          @php
          $arr_parent_category = request('sub_cats');
          $cats = '';
          if(!empty( $arr_parent_category)){
            $cats = App\Models\Category::whereIn('parent_id',  $arr_parent_category)->select('id','en_name')->get();
          }
          @endphp
          @if(!empty($cats))
          @foreach ($cats as  $cat_val)
          <option value="{{ $cat_val->id}}" @if(request('third_cats')){{in_array( $cat_val->id,request('third_cats')) ? 'selected' : ''}}@endif>{{ $cat_val->en_name}}</option>
         @endforeach
         @endif
        </select>
      </div>

        @php
            $currentPath = request()->path();
            $adminScope = explode('/', $currentPath)[0];
        @endphp
        @if( $adminScope != 'provider' )
        <div class="col-md-2 mb-3" style="padding-top: 15px">
            <label class="control-label">{{ __('language.Provider') }} </label>
            <select class="form-control select" name="provider_name[]" title="Select A Provider" multiple>
                {{-- <option selected disabled>{{ __('language.Select A Provider') }}</option> --}}
                @foreach($providers as $provider)
                    <option value="{{$provider->id}}" @if(request('provider_name')){{in_array($provider->id,request('provider_name')) ? 'selected' : ''}}@endif>{{$provider->en_name}}</option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="col-md-2 mb-3" style="padding-top: 15px">
            <label class="control-label"> {{ __('language.Orders Status') }}</label>
            <select class="form-control" name="order_status">
                <option selected disabled>{{ __('language.Select order status') }}</option>
                <option value="open" @if(request('order_status')){{request('order_status') == 'open' ? 'selected' : ''}}@endif>{{ __('language.Open') }}</option>
                <option value="complete" @if(request('order_status')){{request('order_status') == 'complete' ? 'selected' : ''}}@endif>{{ __('language.Complete') }}</option>
                <option value="canceled" @if(request('order_status')){{request('order_status') == 'canceled' ? 'selected' : ''}}@endif>{{ __('language.Canceled') }}</option>
            </select>
        </div>

        <div class="col-md-2 mb-3" style="padding-top: 15px">
            <label class="control-label">{{ __('language.Items Approval Status') }} </label>
            <select class="form-control" name="items_status">
                <option selected disabled>{{ __('language.Select approval status') }}</option>
                <option value="no" @if(request('items_status')){{request('items_status') == 'no' ? 'selected' : ''}}@endif>{{ __('language.Not Required') }}</option>
                <option value="user" @if(request('items_status')){{request('items_status') == 'user' ? 'selected' : ''}}@endif>{{ __('language.Required By User') }}</option>
                <option value="admin" @if(request('items_status')){{request('items_status') == 'admin' ? 'selected' : ''}}@endif>{{ __('language.Required By Admin') }}</option>
            </select>
        </div>

    </div>
    <div class="form-row">
        <div class="col-md-12">
            <a href="{{  Request::url() }}"  class="btn btn-primary" style="float: right">Reset</a>
            <button class="btn btn-default" style="float: right; margin-right: 4px;">{{ __('language.Search now') }}</button>

        </div>
    </div>
      <div class="form-row">
        <div class="col-md-12">
            <label class="control-label">{{ __('language.Price Range') }}</label>
          <input type="text" id="ise_step" name="price_range" value="{{request('price_range')}}" />
        </div>
      </div>

</div>
<p style="float:right; padding-right: 90px">{{ __('language.count') }}: {{ $orders->total() }}</p>
<script type="text/javascript" src="{{asset('admin/js/datepicker/moment.min.js')}}"></script>
<script type="text/javascript" src="{{asset('admin/js/datepicker/daterangepicker.js')}}"></script>
<script>

    {{--$(document).ready(function (e) {--}}
    {{--    var parent_id = $('#company_id').val();--}}
    {{--    if (parent_id) {--}}
    {{--        $.ajax({--}}
    {{--            url: '/provider/get_sub_company/'+parent_id,--}}
    {{--            type: "GET",--}}
    {{--            dataType: "json",--}}
    {{--            success: function (data) {--}}

    {{--                $('#sub_company').empty();--}}
    {{--                $('#sub_company').append('<option selected disabled> Select a Sub Company </option>');--}}
    {{--                $.each(data, function (i, sub_company) {--}}
    {{--                    var selected = [{{  implode(',',request('sub_company') ? request('sub_company') : []) }}]--}}
    {{--                    var includes = selected.includes(sub_company.id)--}}

    {{--                    if(includes == true)--}}
    {{--                        $('#sub_company').append('<option value="' + sub_company.id + '" selected>' + sub_company.en_name + '</option>');--}}
    {{--                    else--}}
    {{--                        $('#sub_company').append('<option value="' + sub_company.id + '">' + sub_company.en_name + '</option>');--}}

    {{--                });--}}
    {{--            }--}}
    {{--        });--}}
    {{--    }--}}
    {{--});--}}

    {{--$('#main_cats').on('change', function (e) {--}}
    {{--    var parent_id = e.target.value;--}}
    {{--    var company_id = $('#company_id').val();--}}
    {{--    if (parent_id) {--}}
    {{--        $.ajax({--}}
    {{--            url: '/provider/'+company_id+'/get_sub_category_provider/'+parent_id,--}}
    {{--            type: "GET",--}}
    {{--            dataType: "json",--}}
    {{--            success: function (data) {--}}
    {{--                $('#sub_cats').empty();--}}
    {{--                $('#sub_cats').append('<option selected disabled> Select a Sub Category </option>');--}}
    {{--                $.each(data, function (i, sub_cat) {--}}
    {{--                    var selected = [{{  implode(',',request('sub_cats') ? request('sub_cats') : []) }}]--}}
    {{--                    var includes = selected.includes(sub_cat.id)--}}

    {{--                    if(includes == true)--}}
    {{--                        $('#sub_cats').append('<option value="' + sub_cat.id + '" selected>' + sub_cat.en_name + '</option>');--}}
    {{--                    else--}}
    {{--                        $('#sub_cats').append('<option value="' + sub_cat.id + '">' + sub_cat.en_name + '</option>');--}}
    {{--                });--}}
    {{--            }--}}
    {{--        });--}}

    {{--    }--}}
    {{--});--}}
</script>

@section('scripts')
  <script type="text/javascript">
      $("#ise_default").ionRangeSlider();
      $("#ise_step").ionRangeSlider({
        type: "double",
        grid: true,
        min: 0,
        max: 10000000,
        from: {{ request('price_range') ? explode(';', request('price_range'))[0] : 0 }},
        to: {{ request('price_range') ? explode(';', request('price_range'))[1] : 10000000 }},
        step: 50
      });
  </script>
@endsection

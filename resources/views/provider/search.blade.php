<div class="form-group">
    <div class="col-md-6 col-xs-12">
        <div class="input-group" style="margin-top: 10px;">
            <input type="text" class="form-control" name="search" value="{{isset($search) ? $search : ''}}" placeholder="Search by SMO No. or Order No. or Technician name or User name" style="margin-top: 1px;"/>
            <span class="input-group-addon btn btn-default">
                <button type="submit" class="btn btn-default">{{ __('language.Search now') }}</button>
                <a class="btn btn-default" data-toggle="collapse" data-target="#advanced_search">
                  <i class="fa fa-sliders"></i>
                  {{ __('language.Advanced Search') }}
                </a>
            </span>
        </div>
    </div>
</div>

<div id="advanced_search" @if(strpos($_SERVER['REQUEST_URI'],'search') !== false) @else class="collapse card-body" @endif>
    <input type="hidden" id="company_id" value="{{isset($company) ? $company->id : ''}}">

    <div class="form-row">
        <div class="col-md-2 mb-3 {{ $errors->has('from') ? ' has-error' : '' }}">
            <label class="control-label">{{ __('language.Date From') }} </label>
            <input type="date" class="form-control" name="from" value="{{request('from')}}">
            @include('admin.layouts.error', ['input' => 'from'])
        </div>

        <div class="col-md-2 mb-3 {{ $errors->has('to') ? ' has-error' : '' }}">
            <label class="control-label">{{ __('language.Date To') }} </label>
            <input type="date" class="form-control" name="to" value="{{request('to')}}">
            @include('admin.layouts.error', ['input' => 'to'])
        </div>
    </div>

    <div class="form-row">
        <div class="col-md-2 mb-3">
            <label class="control-label">‫‪Main‬‬ {{ __('language.Category') }}‬‬ </label>
            <select class="form-control select" id="main_cats" name="main_cats[]" title="Select A Main Category" multiple required>
                @foreach($cats as $cat)
                    <option value="{{$cat->id}}" @if(request('main_cats')){{in_array($cat->id,request('main_cats')) ? 'selected' : ''}}@endif>{{$cat->en_name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 mb-3">
            <label class="control-label">‫‪{{ __('language.Sub Category') }}‬‬ </label>
            <select class="form-control select" name="sub_cats[]" id="sub_cats" title="Select A Category First" multiple>
            </select>
        </div>
    </div>

    <div class="form-row">
        <div class="col-md-2 mb-3" >
          <label class="control-label"> {{ __('language.Sub Company') }}</label>
          <select class="form-control select" id="sub_company" name="sub_company[]" title="Select Company first" multiple>
            {{-- <option selected disabled>Select Company first </option> --}}
          </select>
        </div>
    </div>

    <div class="form-row">
        {{-- <div class="col-md-2 mb-3">
            <label class="control-label"> {{ __('language.Service Type') }}</label>
            <select class="form-control select" name="service_type[]" multiple>
                <option selected disabled>Select service type</option>
                <option value="1" @if(request('service_type')){{in_array(1,request('service_type')) ? 'selected' : ''}}@endif>Preview</option>
                <option value="2" @if(request('service_type')){{in_array(2,request('service_type')) ? 'selected' : ''}}@endif>Maintenance</option>
                <option value="3" @if(request('service_type')){{in_array(3,request('service_type')) ? 'selected' : ''}}@endif>Structure</option>
            </select>
        </div> --}}
        <div class="col-md-2 mb-3" >
            <label class="control-label"> {{ __('language.Type Order') }} </label>
            <select class="form-control select" name="order_type[]" title="Select type order" multiple>
                {{-- <option selected disabled>{{ __('language.Select type order') }}</option> --}}
                <option value="urgent" @if(request('order_type')){{in_array('urgent',request('order_type')) ? 'selected' : ''}}@endif>Urgent</option>
                <option value="scheduled" @if(request('order_type')){{in_array('scheduled',request('order_type')) ? 'selected' : ''}}@endif>Scheduled</option>
                <option value="re_scheduled" @if(request('order_type')){{in_array('re_scheduled',request('order_type')) ? 'selected' : ''}}@endif>Re_scheduled</option>
                <option value="emergency" @if(request('order_type')){{in_array('emergency',request('order_type')) ? 'selected' : ''}}@endif>Emergency</option>
            </select>
        </div>
        @if(strpos($_SERVER['REQUEST_URI'],'provider/orders/') == false)
        @if(isset($providers))
        <div class="col-md-2 mb-3" style="padding-top: 15px">
            <label class="control-label">  {{ __('language.Provider') }} </label>
            <select class="form-control select" name="provider_name[]" title="Select A Provider" multiple>
                {{-- <option selected disabled>Select A Provider</option> --}}
                @foreach($providers as $provider)
                    <option value="{{$provider->id}}" @if(request('provider_name')){{in_array($provider->id,request('provider_name')) ? 'selected' : ''}}@endif>{{$provider->en_name}}</option>
                @endforeach
            </select>
        </div>
        @endif
        @endif

        <div class="col-md-2 mb-3" style="padding-top: 15px">
            <label class="control-label">{{ __('language.Orders Status') }} </label>
            <select class="form-control" name="order_status">
                <option selected disabled>Select order status</option>
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
      <div class="col-md-12" style="padding-top: 15px">
        <label class="control-label">{{ __('language.Price Range') }}</label>
        <input type="text" id="ise_step" name="price_range" value="{{request('price_range')}}" />
      </div>
    </div>
</div>
<p style="float:right; padding-right: 90px">{{ __('language.count') }}: {{ $orders->total() }}</p>

{{--<script>--}}

{{--    $(document).ready(function (e) {--}}
{{--        var parent_id = $('#company_id').val();--}}
{{--        if (parent_id) {--}}
{{--            $.ajax({--}}
{{--                url: '/provider/get_sub_company/'+parent_id,--}}
{{--                type: "GET",--}}

{{--                dataType: "json",--}}

{{--                success: function (data) {--}}
{{--                    $('#sub_company').empty();--}}
{{--                    $('#sub_company').append('<option selected disabled> Select a Sub Company </option>');--}}
{{--                    $.each(data, function (i, sub_company) {--}}
{{--                        var selected = [{{  implode(',',request('sub_company') ? request('sub_company') : []) }}]--}}
{{--                        var includes = selected.includes(sub_company.id)--}}

{{--                        if(includes == true)--}}
{{--                            $('#sub_company').append('<option value="' + sub_company.id + '" selected>' + sub_company.en_name + '</option>');--}}
{{--                        else--}}
{{--                            $('#sub_company').append('<option value="' + sub_company.id + '">' + sub_company.en_name + '</option>');--}}
{{--                    });--}}
{{--                }--}}
{{--            });--}}
{{--        }--}}

{{--        var parent_id = {{request('main_cats')[0]?request('main_cats')[0]:''}};--}}

{{--        if (parent_id) {--}}
{{--            $.ajax({--}}
{{--                url: '/provider/'+company_id+'/get_sub_category_provider/'+parent_id,--}}
{{--                type: "GET",--}}

{{--                dataType: "json",--}}

{{--                success: function (data) {--}}
{{--                    $('#sub_cats').empty();--}}
{{--                    $('#sub_cats').append('<option selected disabled> Select a Sub Category </option>');--}}
{{--                    $.each(data, function (i, sub_cat) {--}}
{{--                        var selected = [{{  implode(',',request('sub_cats') ? request('sub_cats') : []) }}]--}}
{{--                        var includes = selected.includes(sub_cat.id)--}}

{{--                        if (includes == true)--}}
{{--                            $('#sub_cats').append('<option value="' + sub_cat.id + '" selected>' + sub_cat.en_name + '</option>');--}}
{{--                        else--}}
{{--                            $('#sub_cats').append('<option value="' + sub_cat.id + '">' + sub_cat.en_name + '</option>');--}}
{{--                    });--}}
{{--                }--}}
{{--            });--}}
{{--        }--}}
{{--    });--}}

{{--    $('#main_cats').on('change', function (e) {--}}
{{--        var parent_id = e.target.value;--}}
{{--        var company_id = $('#company_id').val();--}}
{{--        if (parent_id) {--}}
{{--            $.ajax({--}}
{{--                url: '/provider/'+company_id+'/get_sub_category_provider/'+parent_id,--}}
{{--                type: "GET",--}}

{{--                dataType: "json",--}}

{{--                success: function (data) {--}}
{{--                    $('#sub_cats').empty();--}}
{{--                    $('#sub_cats').append('<option selected disabled> Select a Sub Category </option>');--}}
{{--                    $.each(data, function (i, sub_cat) {--}}
{{--                        var selected = [{{  implode(',',request('sub_cats') ? request('sub_cats') : []) }}]--}}
{{--                        var includes = selected.includes(sub_cat.id)--}}

{{--                        if(includes == true)--}}
{{--                            $('#sub_cats').append('<option value="' + sub_cat.id + '" selected>' + sub_cat.en_name + '</option>');--}}
{{--                        else--}}
{{--                            $('#sub_cats').append('<option value="' + sub_cat.id + '">' + sub_cat.en_name + '</option>');--}}
{{--                    });--}}
{{--                }--}}
{{--            });--}}

{{--        }--}}
{{--    });--}}

{{--</script>--}}
@section('scripts')
  <script type="text/javascript">
      $("#ise_default").ionRangeSlider();
      $("#ise_step").ionRangeSlider({
        type: "double",
        grid: true,
        min: 0,
        max: 1000000,
        from: {{ request('price_range') ? explode(';', request('price_range'))[0] : 0 }},
        to: {{ request('price_range') ? explode(';', request('price_range'))[1] : 1000000 }},
        step: 50
      });
  </script>
@endsection

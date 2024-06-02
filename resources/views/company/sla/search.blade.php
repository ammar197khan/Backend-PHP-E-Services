<div class="form-group">
    <div class="col-md-6 col-xs-12">
        <div class="input-group" style="margin-top: 10px;">
            <input type="hidden" name="view" value="{{  request('view') }}"/>
            <input type="text" class="form-control" name="search" value="{{isset($search) ? $search : ''}}" placeholder="Search by SMO No. or Order No. or Technician name or User name" style="margin-top: 1px;"/>
            <span class="input-group-addon btn btn-default">
                <button class="btn btn-default">{{ __('language.Search now') }}</button>
                <a class="btn btn-default" data-toggle="collapse" data-target="#advanced_search">
                  <i class="fa fa-sliders"></i>
                  {{ __('language.Advanced Search') }}
                </a>
                <a href="{{  Request::url() }}?view={{ Request::input('view') }}" class="btn btn-primary">Reset</a>
            </span>
        </div>
    </div>
</div>

<div id="advanced_search"  @if(strpos($_SERVER['REQUEST_URI'],'search') !== false) @else class="collapse card-body" @endif>

    <div class="form-row">
        <div class="col-md-2 mb-3 {{ $errors->has('date') ? ' has-error' : '' }}">
            <label class="control-label">Date</label>
            <input type="date" class="form-control" name="date" value="{{request('date')}}">
            @include('admin.layouts.error', ['input' => 'date'])
        </div>

        {{-- <div class="col-md-2 mb-3" style="padding-top: 15px">
            <label class="control-label select">Service Type</label>
            <select class="form-control" name="type">
                <option selected disabled>Select an option</option>
                <option value="urgent" @if(request('type')){{request('type') == 'urgent' ? 'selected' : ''}}@endif>Urgent</option>
                <option value="scheduled" @if(request('type')){{request('type') == 'scheduled' ? 'selected' : ''}}@endif>Scheduled</option>
                <option value="re_scheduled" @if(request('type')){{request('type') == 're_scheduled' ? 'selected' : ''}}@endif>re_scheduled</option>
                <option value="canceled" @if(request('type')){{request('type') == 'canceled' ? 'selected' : ''}}@endif>Canceled</option>
                <option value="emergency" @if(request('type')){{request('type') == 'emergency' ? 'selected' : ''}}@endif>Emergency</option>
            </select>
        </div> --}}
        <div class="col-md-2 mb-3">
            <label class="control-label">Service Category</label>
            <select class="form-control select" id="main_cats" name="cat_id" required>
                <option selected disabled>Select A Main Category</option>
                @foreach($cats as $cat)
                    <option value="{{$cat->id}}" @if(request('cat_id')){{ $cat->id == request('cat_id') ? 'selected' : ''}}@endif>{{$cat->en_name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 mb-3">
            <label class="control-label">Sub Service Category</label>
            <select class="form-control" name="sub_cat_id" id="sub_cats">
                <option selected disabled>Select A Category First</option>
            </select>
        </div>

        <div class="col-md-2 mb-3" >
            <label class="control-label"> Response Time</label>
            <select class="form-control select" id="response_time" name="response_time">
              <option selected disabled>HH:MM </option>
              @php
              for($hours=0; $hours<24; $hours++) // the interval for hours is '1'
              for($mins=0; $mins<60; $mins++) // the interval for mins is '30'
          echo '<option value="'.str_pad($hours,2,'0',STR_PAD_LEFT).':'
                         .str_pad($mins,2,'0',STR_PAD_LEFT).'">'.str_pad($hours,2,'0',STR_PAD_LEFT).':'
                         .str_pad($mins,2,'0',STR_PAD_LEFT).'</option>';
                         @endphp
            </select>
          </div>
          <div class="col-md-2 mb-3" >
            <label class="control-label"> Assessment Time</label>
            <select class="form-control select" id="assessment_time" name="assessment_time">
              <option selected disabled>HH:MM </option>
              @php
              for($hours=0; $hours<24; $hours++) // the interval for hours is '1'
              for($mins=0; $mins<60; $mins++) // the interval for mins is '30'
          echo '<option value="'.str_pad($hours,2,'0',STR_PAD_LEFT).':'
                         .str_pad($mins,2,'0',STR_PAD_LEFT).'">'.str_pad($hours,2,'0',STR_PAD_LEFT).':'
                         .str_pad($mins,2,'0',STR_PAD_LEFT).'</option>';
                         @endphp
            </select>
          </div>
    </div>
    <div class="form-row">


        <div class="col-md-2 mb-3" >
            <label class="control-label"> Rectification Time</label>
            <select class="form-control select" id="rectification_time" name="rectification_time">
              <option selected disabled>HH:MM </option>
              @php
              for($hours=0; $hours<24; $hours++) // the interval for hours is '1'
      for($mins=0; $mins<60; $mins++) // the interval for mins is '30'
          echo '<option value="'.str_pad($hours,2,'0',STR_PAD_LEFT).':'
                         .str_pad($mins,2,'0',STR_PAD_LEFT).'">'.str_pad($hours,2,'0',STR_PAD_LEFT).':'
                         .str_pad($mins,2,'0',STR_PAD_LEFT).'</option>';
                         @endphp
            </select>
          </div>


        <div class="col-md-2 mb-3" style="padding-top: 15px">
            <label class="control-label">Breach Response Time</label>
            <select class="form-control" name="breach_response_time">
                <option selected disabled>Select an option</option>
                <option value="Y" @if(request('breach_response_time')){{request('breach_response_time') == 'Y' ? 'selected' : ''}}@endif>Yes</option>
                <option value="N" @if(request('breach_response_time')){{request('breach_response_time') == 'N' ? 'selected' : ''}}@endif>No</option>
            </select>
        </div>

        <div class="col-md-2 mb-3" style="padding-top: 15px">
            <label class="control-label">Breach Assessment Time</label>
            <select class="form-control" name="breach_assessment_time">
                <option selected disabled>Select an option</option>
                <option value="Y" @if(request('breach_assessment_time')){{request('breach_assessment_time') == 'Y' ? 'selected' : ''}}@endif>Yes</option>
                <option value="N" @if(request('breach_assessment_time')){{request('breach_assessment_time') == 'N' ? 'selected' : ''}}@endif>No</option>
            </select>
        </div>
        <div class="col-md-2 mb-3" style="padding-top: 15px">
            <label class="control-label">Breach Rectification Time</label>
            <select class="form-control" name="breach_rectification_time">
                <option selected disabled>Select an option</option>
                <option value="Y" @if(request('breach_rectification_time')){{request('breach_rectification_time') == 'Y' ? 'selected' : ''}}@endif>Yes</option>
                <option value="N" @if(request('breach_rectification_time')){{request('breach_rectification_time') == 'N' ? 'selected' : ''}}@endif>No</option>
            </select>
        </div>

    </div>


</div>
{{-- <p style="float:right; padding-right: 90px">{{ __('language.count') }}: {{ $orders->total() }}</p> --}}

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

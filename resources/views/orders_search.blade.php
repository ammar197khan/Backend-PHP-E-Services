<div class="form-group">
    <div class="col-md-6 col-xs-12">
        <div class="input-group" style="margin-top: 10px;">
            <input type="text" class="form-control" name="search" value="{{isset($search) ? $search : ''}}" placeholder="Search by SMO No. or Order No. or Technician name or User name" style="margin-top: 1px;"/>
            <span class="input-group-addon btn btn-default">
                                            <button class="btn btn-default">{{ __('language.Search now') }}</button>
                                    </span>
        </div>
    </div>
</div>

<div class="form-group">
<div class="col-md-4 col-xs-4">
<div class="input-group">
<select class="form-control select" id="company_id" name="company_id">
<option selected disabled>Select Company</option>
@foreach($companies as $company)
<option value="{{$company->id}}">{{$company->en_name}}</option>
@endforeach
</select>
<span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
</div>
</div>
</div>

<div class="form-group col-md-4">
    <select class="form-control" id="sub_company" name="sub_company[]" multiple>
        <option selected disabled>Select Company first </option>
    </select>
</div>
<div class="form-group col-md-4 {{ $errors->has('from') ? ' has-error' : '' }}">
    <label class="col-md-2 control-label">{{ __('language.From') }} </label>
    <div class="input-group">
        <input type="date" class="form-control" name="from" value="{{request('from')}}">
        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
    </div>
    @include('admin.layouts.error', ['input' => 'from'])
</div>

<div class="form-group col-md-4 {{ $errors->has('to') ? ' has-error' : '' }}">
    <label class="col-md-2 control-label">To </label>
    <div class="input-group">
        <input type="date" class="form-control" name="to" value="{{request('to')}}">
        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
    </div>
    @include('admin.layouts.error', ['input' => 'to'])
</div>
<div class="form-group col-md-3">
    <select class="form-control select" id="main_cats" name="main_cats" required>
        <option selected disabled>Select A Main Category</option>
        @foreach($cats as $cat)
            <option value="{{$cat->id}}">{{$cat->en_name}}</option>
        @endforeach
    </select>
</div>
<div class="form-group col-md-3">
    <select class="form-control" name="sub_cats" data-style="btn-success" id="sub_cats">
        <option selected disabled>Select A Category First</option>
    </select>
</div>
<div class="form-group col-md-3">
    <select class="form-control select" name="service_type[]" multiple>
        <option selected disabled>Select service type</option>
        <option value="1">Preview</option>
        <option value="2">Maintenance</option>
        <option value="3">Structure</option>
    </select>
</div>
<div class="form-group">
    <div class="col-md-10">
        <p>Price Range</p>
        <input type="text" id="ise_step" name="price_range" value="" />
    </div>
</div>

<script>

    $('#company_id').on('change', function (e) {
        var parent_id = $('#company_id').val();
        if (parent_id) {
            $.ajax({
                url: '/provider/get_sub_company/'+parent_id,
                type: "GET",

                dataType: "json",

                success: function (data) {
                    $('#sub_company').empty();
                    $('#sub_company').append('<option selected disabled> Select a Sub Company </option>');
                    $.each(data, function (i, sub_company) {
                        $('#sub_company').append('<option value="' + sub_company.id + '">' + sub_company.en_name + '</option>');
                    });
                }
            });

        }
    });

    $('#main_cats').on('change', function (e) {
        var parent_id = e.target.value;
        var company_id = $('#company_id').val();
        if (parent_id) {
            $.ajax({
                url: '/provider/'+company_id+'/get_sub_category_provider/'+parent_id,
                type: "GET",

                dataType: "json",

                success: function (data) {
                    $('#sub_cats').empty();
                    $('#sub_cats').append('<option selected disabled> Select a Sub Category </option>');
                    $.each(data, function (i, sub_cat) {
                        $('#sub_cats').append('<option value="' + sub_cat.id + '">' + sub_cat.en_name + '</option>');
                    });
                }
            });

        }
    });

</script>

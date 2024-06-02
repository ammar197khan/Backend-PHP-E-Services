@extends('company.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/company/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li><a href="/company/appliances">{{ __('language.Appliances') }}</a></li>
        <li class="active">{{isset($item) ? 'Update an admin' : 'Create a admin'}}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <div class="page-content-wrap">

        <div class="row">
            <div class="col-md-12">
                @include('admin.layouts.message')
                <form class="form-horizontal" method="post" action="{{isset($item) ? '/company/appliances/update' : '/company/appliances/store'}}" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">{{isset($item) ? 'Update an admin' :  __('language.Create an admin') }}</h3>
                        </div>
                        <div class="panel-body">

                            <div class="panel-body">

                                <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label class="col-md-3 col-xs-12 control-label">Name</label>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="name" value="{{isset($item) ? $item->name : old('name')}}" required/>
                                            <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                        </div>
                                        @include('admin.layouts.error', ['input' => 'name'])
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('munufucturer') ? ' has-error' : '' }}">
                                    <label class="col-md-3 col-xs-12 control-label">{{ __('language.Munufucturer') }}</label>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="munufucturer" value="{{isset($item) ? $item->munufucturer : old('munufucturer')}}" required/>
                                            <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                        </div>
                                        @include('admin.layouts.error', ['input' => 'munufucturer'])
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('model') ? ' has-error' : '' }}">
                                    <label class="col-md-3 col-xs-12 control-label">{{ __('language.Model') }}</label>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="model" value="{{isset($item) ? $item->model : old('model')}}" required/>
                                            <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                        </div>
                                        @include('admin.layouts.error', ['input' => 'model'])
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('quantity') ? ' has-error' : '' }}">
                                    <label class="col-md-3 col-xs-12 control-label">{{ __('language.QTY') }}</label>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="quantity" value="{{isset($item) ? $item->quantity : old('quantity')}}" required/>
                                            <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                        </div>
                                        @include('admin.layouts.error', ['input' => 'quantity'])
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('serial_number') ? ' has-error' : '' }}">
                                    <label class="col-md-3 col-xs-12 control-label">{{ __('language.Serial Number') }}</label>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="serial_number" value="{{isset($item) ? $item->serial_number : old('serial_number')}}" required/>
                                            <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                        </div>
                                        @include('admin.layouts.error', ['input' => 'serial_number'])
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('warranty_deadline') ? ' has-error' : '' }}">
                                    <label class="col-md-3 col-xs-12 control-label">{{ __('language.Warranty Deadline') }}</label>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="input-group">
                                            <input type="date" class="form-control" name="warranty_deadline" value="{{isset($item) ? $item->warranty_deadline : old('warranty_deadline')}}" required/>
                                            <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                        </div>
                                        @include('admin.layouts.error', ['input' => 'warranty_deadline'])
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('condition') ? ' has-error' : '' }}">
                                    <label class="col-md-3 col-xs-12 control-label">{{ __('language.Condition') }}</label>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="input-group">
                                            <select class="form-control" name="condition">
                                              <option value="excellent">{{ __('language.Excellent') }}</option>
                                              <option value="very good">{{ __('language.Very Good') }}</option>
                                              <option value="good">{{ __('language.Good') }}</option>
                                              <option value="fine">{{ __('language.Fine') }}</option>
                                              <option value="bad">{{ __('language.Bad') }}</option>
                                              <option value="damaged">{{ __('language.Damaged') }}</option>
                                            </select>
                                            <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                        </div>
                                        @include('admin.layouts.error', ['input' => 'condition'])
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('description') ? ' has-error' : '' }}">
                                    <label class="col-md-3 col-xs-12 control-label">{{ __('language.Description') }}</label>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="input-group">
                                            <textarea class="form-control" name="description" rows="8" cols="80">{{ isset($item) ? optional($item)->description : '' }}</textarea>
                                            <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                        </div>
                                        @include('admin.layouts.error', ['input' => 'description'])
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('location') ? ' has-error' : '' }}">
                                    <label class="col-md-3 col-xs-12 control-label">{{ __('language.Location') }}</label>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="location" value="{{isset($item) ? $item->location : old('location')}}" required/>
                                            <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                        </div>
                                        @include('admin.layouts.error', ['input' => 'location'])
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('photo') ? ' has-error' : '' }}">
                                    <label class="col-md-3 col-xs-12 control-label">Image</label>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="input-group">
                                            <input type="file" class="fileinput btn-info" name="photo" id="cp_photo" data-filename-placement="inside" title="Select Image"/>
                                        </div>
                                        @if(isset($item))
                                            <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                        @endif
                                        @include('admin.layouts.error', ['input' => 'photo'])
                                    </div>
                                </div>

                          </div>

                        <div class="panel-footer">
                            <button type="reset" class="btn btn-default">{{ __('language.Reset') }}</button> &nbsp;
                            <button class="btn btn-primary pull-right">
                                {{isset($item) ? 'Update' : 'Create'}}
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <script>

        $('#role_select').on('change', function (e) {
            var role = e.target.value;
            $('.permission').hide();
            $('.' + role).show()
        });

    </script>
@endsection

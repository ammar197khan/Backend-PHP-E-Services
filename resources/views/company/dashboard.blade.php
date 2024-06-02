@extends('company.layouts.app')
@section('content')
    @if(company()->hasPermissionTo('Show dashboard'))
        @include('admin.home.home', $data)
    @endif
@endsection

@extends('admin.layouts.app')
@section('content')
    @if(admin()->hasPermissionTo('Show dashboard'))
        @include('admin.home.home', $data)
    @endif
@endsection

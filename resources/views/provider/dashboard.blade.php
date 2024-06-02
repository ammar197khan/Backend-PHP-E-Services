@extends('provider.layouts.app')
@section('content')
    @if(provider()->hasPermissionTo('Show dashboard'))
        @include('admin.home.home', $data)
    @endif
@endsection

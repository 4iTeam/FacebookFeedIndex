@extends('layouts.base')

@section('header')
    @include('layouts.app.header')
@endsection
@section('footer')
    @include('layouts.app.footer')
@endsection
@section('page')
    @include('layouts.app.sidebar')
    <!-- Main content -->
    <main class="main">
        @if(!isset($exception))
        @include('layouts.app.breadcrumb')
        @endif

        <div class="container-fluid">
            <div class="animated fadeIn">
                @yield('content')
            </div>

        </div>
        <!-- /.conainer-fluid -->
    </main>

    {{-- @include('layouts.app.aside') --}}
@endsection

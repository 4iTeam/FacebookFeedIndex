<!DOCTYPE html>
<html lang="en">

<head>
@include('partials.head')
</head>
{{--
<!-- BODY options, add following classes to body to change options

// Header options
1. '.header-fixed'					- Fixed Header

// Brand options
1. '.brand-minimized'       - Minimized brand (Only symbol)

// Sidebar options
1. '.sidebar-fixed'					- Fixed Sidebar
2. '.sidebar-hidden'				- Hidden Sidebar
3. '.sidebar-off-canvas'		- Off Canvas Sidebar
4. '.sidebar-minimized'			- Minimized Sidebar (Only icons)
5. '.sidebar-compact'			  - Compact Sidebar

// Aside options
1. '.aside-menu-fixed'			- Fixed Aside Menu
2. '.aside-menu-hidden'			- Hidden Aside Menu
3. '.aside-menu-off-canvas'	- Off Canvas Aside Menu

// Breadcrumb options
1. '.breadcrumb-fixed'			- Fixed Breadcrumb

// Footer options
1. '.footer-fixed'					- Fixed footer

-->
--}}

<body class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden">
@include('partials.bodyscripts')
@section('header')
<!-- Header -->

<!-- End of Header -->
@show

<div class="container">
	<?php if ( isset($errors) && $errors->has( 'error' ) )
		Message::error( $errors->first( 'error' ) );
	?>
    @if(Message::has())
        <div class="row">
            <div class="col-lg-12">
                {!! Message::display(true) !!}
            </div>
        </div>
    @endif
</div>

<div class="app-body">

    @yield('page')


</div>


@section('footer')

@show

<script src="{{url('js/la.js')}}"></script>

<script src="{{url('vendor/bootstrap4/js/bootstrap.min.js')}}"></script>
<script src="{{url('js/app.js')}}"></script>
{{-- Modules js--}}
<script src="{{asset('js/global.js')}}"></script>
{{-- Other js--}}
@stack('scripts')

@include('partials.footscripts')
</body>

</html>
<title>@yield('title',e($site->title))</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="{{$site->description}}">
<meta name="author" content="">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta property="og:type" content="website"/>
<meta property="og:title" content="@yield('title',e($site->title))"/>
<meta property="og:image" content="{{$site->image}}"/>
<meta property="og:site_name" content="@yield('title',e($site->title))"/>
<meta property="og:description" content="{{$site->description}}"/>
<meta property="og:url" content="{{ url()->current() }}"/>
<meta property="fb:app_id" content="{!! config('services.facebook.client_id') !!}"/>
@if($site->favicon)
<link rel="icon" type="image/png" href="{{$site->favicon}}" sizes="16x16">
@if($site->favicon32)
<link rel="icon" type="image/png" href="{{$site->favicon32}}" sizes="32x32">
@endif
@else
<link rel="apple-touch-icon" sizes="180x180" href="{{asset('/images/favicon/apple-touch-icon.png')}}">
<link rel="icon" type="image/png" href="{{asset('/images/favicon/favicon-32x32.png')}}" sizes="32x32">
<link rel="icon" type="image/png" href="{{asset('/images/favicon/favicon-16x16.png')}}" sizes="16x16">
<link rel="manifest" href="{{asset('/images/favicon/manifest.json')}}">
<link rel="mask-icon" href="{{asset('/images/favicon/safari-pinned-tab.svg')}}" color="#5bbad5">
<meta name="theme-color" content="#ffffff">
@endif
<meta name="generator" content="WordPress 5.3.0" />
<style>body{display:none;}</style>
<!-- Fonts CSS -->
<noscript id="deferred-styles">
    <link href="{{asset('vendor/simple-line-icons/css/simple-line-icons.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('vendor/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('css/style.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('css/app.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('wp-content/themes/laravel-admin/style.css')}}" rel="stylesheet" type="text/css">
    @stack('styles')
</noscript>



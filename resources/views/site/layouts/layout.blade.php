<!DOCTYPE html>
<!--[if IE 8]> <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="{{ str_replace('_', '-', app()->getLocale()) }}"> <!--<![endif]-->
<head>
    <title>@yield('app_title', env('APP_NAME'))E2</title>
    <meta name="description" content="@yield('app_title', env('APP_NAME'))E2">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

     <link rel="preconnect" href="https://fonts.bunny.net">
     <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="icon" href="{{asset('favicon.png')}}" type="image/png"/>
    <link rel="stylesheet" href="{{ asset('css/style.css?v=1.01') }}">
    <script src="{{ asset('js/script.js?v=1.00') }}"></script>  
    
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>                
            </style>
        @endif
        
       <script src="{{ asset('js/lightbox.min.js') }}"></script> 
       <link rel="stylesheet" href="{{ asset('js/lightbox.min.css') }}">
                                    
</head>

<body>
@include('site.layouts.header')


<div id="app">
@yield('content')
</div>


@include('site.layouts.footer')
@include('site.layouts.script')
</body>
</html>


{{-- Страницы --}}
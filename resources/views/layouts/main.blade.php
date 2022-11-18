<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/x-icon" href="/favicon.png">
    @if(request()->cookie('theme') == 'dark')
        <link id="mainStyle" href="/css/bootstrap-cyborg.min.css" rel="stylesheet" data-theme="dark">
    @else
        <link id="mainStyle" href="/css/bootstrap.min.css" rel="stylesheet" data-theme="light">
    @endif
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">

    <title>ScanDeso - @yield('title')</title>
</head>
<body class="test d-flex flex-column min-vh-100 {{ request()->cookie('theme') == 'dark' ? 'dark' : 'light' }}">
    <x-header></x-header>
    <main class="mb-4">
        <div class="container-xxl">
            @yield('content')
        </div>
    </main>
    <x-footer></x-footer>
    <script src="{{ mix('/js/app.js') }}"></script>
    @stack('js')
</body>
</html>

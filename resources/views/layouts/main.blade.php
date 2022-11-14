<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/x-icon" href="/favicon.png">
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
{{--    <link href="https://bootswatch.com/5/cyborg/bootstrap.min.css" rel="stylesheet">--}}

    <title>ScanDeso - @yield('title')</title>
</head>
<body>
    <x-header></x-header>
    <main>
        <div class="container-xxl">
            @yield('content')
        </div>
    </main>
{{--    <x-footer></x-footer>--}}
    <script src="{{ mix('/js/app.js') }}"></script>
    @stack('js')
</body>
</html>

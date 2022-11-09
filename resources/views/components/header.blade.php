<nav class="navbar navbar-expand-lg bg-light">
    <div class="container-fluid container-xxl">
        <a class="brand navbar-brand" href="{{ route('home') }}">ScanDeso</a>
        <span><small>DESO: ${{ $desoDesoPrice }}</small></span>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link @if(Request::routeIs('home')) active @endif" aria-current="page" href="{{ route('home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if(Request::routeIs('about')) active @endif" href="{{ route('about') }}">About</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

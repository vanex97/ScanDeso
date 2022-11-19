@if ($pages > 1)
    <nav>
        <ul class="pagination justify-content-md-end justify-content-center flex-wrap">
            <li class="page-item @if($page == 1) disabled @endif">
                <a class="page-link" href="{{ route('address', ['address' => $address]) }}">First</a>
            </li>
            <li class="page-item @if($page == 1) disabled @endif">
                <a class="page-link" href="{{ route('address', ['address' => $address, 'page' => $page - 1]) }}"><</a>
            </li>
            <li class="page-item">
                <span class="page-link">{{ $page }} of {{ $pages }}</span>
            </li>
            <li class="page-item @if($page == $pages) disabled @endif">
                <a class="page-link" href="{{ route('address', ['address' => $address, 'page' => $page + 1]) }}">></a>
            </li>
            <li class="page-item @if($page == $pages) disabled @endif">
                <a class="page-link" href="{{ route('address', ['address' => $address, 'page' => $pages]) }}">Last</a>
            </li>
        </ul>
    </nav>
@endif

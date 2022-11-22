@extends('layouts.main')

@section('title', '@' . $user['Username'])

@section('content')
    <div class="mt-3">
        <x-search></x-search>
    </div>

    <div class="user-info d-flex align-items-center mt-5">
        <img src="https://node.deso.org/api/v0/get-single-profile-picture/{{ $user['PublicKeyBase58Check'] }}?fallback=https://diamondapp.com/assets/img/default_profile_pic.png"
             class="user-info__logo me-3"
             alt="user-logo">
        <div class="user-info__credentials">
            <a class="user-info__username text-decoration-none text-break" href="https://diamondapp.com/u/{{ $user['Username'] }}" target="_blank">
                {{ '@' . $user['Username'] }}
            </a>
            <div class="user-info__address text-secondary text-break">
                {{ $user['PublicKeyBase58Check'] }}
            </div>
        </div>
    </div>

    <div class="d-flex">
        <div class="card mt-5 col">
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between flex-wrap">
                    <span class="me-2">Deso Price:</span>
                    <span>${{ $desoDesoPrice }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between flex-wrap">
                    <span class="me-2">Balance:</span>
                    <span>
                        {{ \App\Helpers\CurrencyHelper::nanoToDeso($user['DESOBalanceNanos'], null) }}
                        DESO ≈ ${{ \App\Helpers\CurrencyHelper::nanoToDollars($user['DESOBalanceNanos'], $desoDesoPrice) }}
                    </span>
                </li>
                @if($transactionQuantity)
                    <li class="list-group-item d-flex justify-content-between flex-wrap">
                        <span class="me-2">Total transactions:</span>
                        <span>{{ number_format($transactionQuantity) }}</span>
                    </li>
                @endif
{{--                <li class="list-group-item d-flex justify-content-between">--}}
{{--                    <span>Account age:</span>--}}
{{--                    <span>...</span>--}}
{{--                </li>--}}
            </ul>
        </div>
{{--        <div class="card mt-5 col">--}}
{{--            <ul class="list-group list-group-flush">--}}
{{--                <li class="list-group-item">Coins in Circulation</li>--}}
{{--                <li class="list-group-item">Total USD Locked</li>--}}
{{--                <li class="list-group-item">USD Market Cap</li>--}}
{{--            </ul>--}}
{{--        </div>--}}
    </div>
    <x-transactions :$transactions :$user></x-transactions>
    <x-transactions-pagination :$address :$page :$transactionQuantity></x-transactions-pagination>
@endsection

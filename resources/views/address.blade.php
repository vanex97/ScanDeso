@extends('layouts.main')

@section('title', '@' . $user['Username'])

@section('content')
    <div class="mt-3">
        <x-search></x-search>
    </div>

    <div class="user-info d-flex align-items-center mt-5">
        <img src="https://node.deso.org/api/v0/get-single-profile-picture/{{ $user['PublicKeyBase58Check'] }}"
             class="user-info__logo me-3"
             alt="user-logo">
        <div class="user-info__credentials">
            <a class="user-info__username text-decoration-none" href="https://diamondapp.com/u/{{ $user['Username'] }}" target="_blank">
                {{ '@' . $user['Username'] }}
            </a>
            <div class="user-info__address text-secondary">
                {{ $user['PublicKeyBase58Check'] }}
            </div>
        </div>
    </div>

    <div class="d-flex ">
        <div class="card mt-5 col me-3 col-3">
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span>Deso Price:</span>
                    <span>${{ $desoDesoPrice }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Balance:</span>
                    <span>
                        {{ \App\Helpers\CurrencyHelper::nanoToDeso($user['DESOBalanceNanos'], null) }}
                        DESO ≈ ${{ \App\Helpers\CurrencyHelper::nanoToDollars($user['DESOBalanceNanos'], $desoDesoPrice) }}
                    </span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total transactions:</span>
                    <span>{{ number_format($transactionQuantity) }}</span>
                </li>
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

    <table class="table table-bordered mt-5">
        <thead>
        <tr>
            <th scope="col">Transaction ID</th>
            <th scope="col">Type</th>
            <th scope="col">From</th>
            <th scope="col"></th>
            <th scope="col">To</th>
            <th scope="col">Value</th>
            <th scope="col">Fee Nanos</th>
        </tr>
        </thead>
        <tbody>
        @foreach($transactions as $transaction)
            @if(!$transaction['TransactionMetadata']['TxnOutputs'])
                @continue
            @endif

            <tr>
                <td>
                    <a class="text-truncate hash-tag" href="{{ route('transaction', ['transactionId' => $transaction['TransactionIDBase58Check']]) }}">
                        {{ $transaction['TransactionIDBase58Check'] }}
                    </a>
                </td>
                <td>
                    {{ \App\Helpers\StringHelper::formatTransactionType($transaction['TransactionType']) }}
                    @if (isset($transaction['TransactionMetadata']['FollowTxindexMetadata']['IsUnfollow'])
                         && $transaction['TransactionMetadata']['FollowTxindexMetadata']['IsUnfollow'])
                        (Unfollow)
                    @elseif(isset($transaction['TransactionMetadata']['LikeTxindexMetadata']['IsUnlike'])
                            && $transaction['TransactionMetadata']['LikeTxindexMetadata']['IsUnlike'])
                        (Dislike)
                    @endif
                </td>
                <td>
                    <a href="{{ route('address', $transaction['TransactionMetadata']['AffectedPublicKeys'][0]['PublicKeyBase58Check']) }}"
                       class="text-truncate hash-tag"
                       data-bs-toggle="tooltip"
                       data-bs-placement="top"
                       data-bs-custom-class="address-tooltip"
                       data-bs-title="{{ $transaction['TransactionMetadata']['AffectedPublicKeys'][0]['PublicKeyBase58Check'] }}">
                        {{ $transaction['TransactionMetadata']['AffectedPublicKeys'][0]['PublicKeyBase58Check'] }}
                    </a>
                </td>
                <td class="text-center">
                    @if ($transaction['TransactionMetadata']['AffectedPublicKeys'][0]['PublicKeyBase58Check'] == $user['PublicKeyBase58Check'])
                        <span class="badge rounded-pill text-bg-warning operation-badge">Out</span>
                    @else
                        <span class="badge rounded-pill text-bg-success operation-badge">In</span>
                    @endif
                </td>
                <td>
                    @if($transaction['TransactionType'] != 'UPDATE_PROFILE')
                        @if(count($transaction['TransactionMetadata']['AffectedPublicKeys']) == 2)
                            <a href="{{ route('address', $transaction['TransactionMetadata']['AffectedPublicKeys'][1]['PublicKeyBase58Check']) }}"
                               class="text-truncate hash-tag"
                               data-bs-toggle="tooltip"
                               data-bs-placement="top"
                               data-bs-custom-class="address-tooltip"
                               data-bs-title="{{ $transaction['TransactionMetadata']['AffectedPublicKeys'][1]['PublicKeyBase58Check'] }}">
                                {{ $transaction['TransactionMetadata']['AffectedPublicKeys'][1]['PublicKeyBase58Check'] }}
                            </a>
                        @elseif(count($transaction['TransactionMetadata']['AffectedPublicKeys']) > 2)
                            <div class="accordion accordion-flush" id="accordion-{{ $loop->iteration }}">
                                <div class="accordion-item">
                                    <button class="accordion-button p-0 collapsed"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#flush-collapse-{{ $loop->iteration }}"
                                            aria-controls="flush-collapse-{{ $loop->iteration }}">
                                        {{ count($transaction['TransactionMetadata']['AffectedPublicKeys']) - 1 }} addresses
                                    </button>
                                    <div id="flush-collapse-{{ $loop->iteration }}"
                                         class="collapse clickable"
                                         aria-labelledby="flush-heading-{{ $loop->iteration }}"
                                         data-bs-parent="#accordion-{{ $loop->iteration }}">
                                            @foreach($transaction['TransactionMetadata']['AffectedPublicKeys'] as $affectedTransaction)
                                                @if($loop->iteration == 1)
                                                    @continue
                                                @endif
                                                <div class="row mt-1">
                                                    <a href="{{ route('address', $affectedTransaction['PublicKeyBase58Check']) }}"
                                                       class="text-truncate hash-tag"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top"
                                                       data-bs-custom-class="address-tooltip"
                                                       data-bs-title="{{ $affectedTransaction['PublicKeyBase58Check'] }}">
                                                        {{ $affectedTransaction['PublicKeyBase58Check'] }}
                                                    </a>
                                                </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        -
                    @endif
                </td>
                <td data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    data-bs-title="{{ \App\Helpers\CurrencyHelper::nanoToDeso($transaction['Outputs'][0]['AmountNanos'], null) }} DESO">
                    {{ \App\Helpers\CurrencyHelper::nanoToDeso($transaction['Outputs'][0]['AmountNanos']) }} DESO
                </td>
                <td>
                    {{ \App\Helpers\CurrencyHelper::nanoToDeso($transaction['TransactionMetadata']['BasicTransferTxindexMetadata']['FeeNanos'], 7) }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <x-transactions-pagination :$address :$page :$transactionQuantity></x-transactions-pagination>
@endsection

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
                <li class="list-group-item d-flex justify-content-between flex-wrap">
                    <span class="me-2">Total transactions:</span>
                    <span>{{ number_format($transactionQuantity) }}</span>
                </li>
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
    <div class="table-responsive mb-3">
        <table class="table table-bordered mt-5 mb-0">
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
                        @php $transactionSubType = \App\Helpers\TransactionHelper::getSubtype($transaction) @endphp

                        @if(isset($transaction['ExtraData']['DiamondPostHash']) && $transaction['TransactionType'] == 'BASIC_TRANSFER')
                            💎
                        @endif

                        @if($transactionSubType)
                            ({{ $transactionSubType }})
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('address', $transaction['TransactionMetadata']['TransactorPublicKeyBase58Check']) }}"
                           class="text-truncate hash-tag"
                           data-bs-toggle="tooltip"
                           data-bs-placement="top"
                           data-bs-custom-class="address-tooltip"
                           data-bs-title="{{ $transaction['TransactionMetadata']['TransactorPublicKeyBase58Check'] }}">
                            {{ $transaction['TransactionMetadata']['TransactorPublicKeyBase58Check'] }}
                        </a>
                    </td>
                    <td class="text-center">
                        @if(isset($transaction['TransactionMetadata']['CreatorCoinTxindexMetadata']['OperationType']))
                            @if($transaction['TransactionMetadata']['CreatorCoinTxindexMetadata']['OperationType'] == 'buy')
                                <span class="badge rounded-pill text-bg-success operation-badge">Buy</span>
                            @elseif($transaction['TransactionMetadata']['CreatorCoinTxindexMetadata']['OperationType'] == 'sell')
                                <span class="badge rounded-pill text-bg-danger operation-badge">Sell</span>
                            @else
                                <span class="badge rounded-pill text-bg-primary operation-badge">
                                    {{ ucfirst($transaction['TransactionMetadata']['CreatorCoinTxindexMetadata']['OperationType']) }}
                                </span>
                            @endif
                        @elseif($transaction['TransactionMetadata']['TransactorPublicKeyBase58Check'] == $user['PublicKeyBase58Check'])
                            <span class="badge rounded-pill text-bg-warning operation-badge">Out</span>
                        @else
                            <span class="badge rounded-pill text-bg-success operation-badge">In</span>
                        @endif
                    </td>
                    <td>
                        @php $transactionInputs = \App\Helpers\TransactionHelper::getTransferInputs($transaction['TransactionMetadata']['AffectedPublicKeys']); @endphp

                        @if(!$transactionInputs || $transaction['TransactionType'] == 'UPDATE_PROFILE')
                            -
                        @elseif(count($transactionInputs) == 1)
                            <a href="{{ route('address', $transactionInputs[0]['PublicKeyBase58Check']) }}"
                               class="text-truncate hash-tag"
                               data-bs-toggle="tooltip"
                               data-bs-placement="top"
                               data-bs-custom-class="address-tooltip"
                               data-bs-title="{{ $transactionInputs[0]['PublicKeyBase58Check'] }}">
                                {{ $transactionInputs[0]['PublicKeyBase58Check'] }}
                            </a>
                        @elseif(count($transactionInputs) >= 2)
                            <div class="accordion accordion-flush" id="accordion-{{ $loop->iteration }}">
                                <div class="accordion-item">
                                    <button class="accordion-button p-0 collapsed"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#flush-collapse-{{ $loop->iteration }}"
                                            aria-controls="flush-collapse-{{ $loop->iteration }}">
                                        {{ count($transactionInputs) }} addresses @if($transaction['TransactionType'] == 'SUBMIT_POST') mentioned @endif
                                    </button>
                                    <div id="flush-collapse-{{ $loop->iteration }}"
                                         class="collapse clickable"
                                         aria-labelledby="flush-heading-{{ $loop->iteration }}"
                                         data-bs-parent="#accordion-{{ $loop->iteration }}">
                                        @foreach($transactionInputs as $affectedTransaction)
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
                    </td>

                    @if(\App\Helpers\TransactionHelper::getValueByType($transaction))
                        <td data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            data-bs-title="{{ \App\Helpers\TransactionHelper::getValueByType($transaction, null) }} {{ \App\Helpers\TransactionHelper::getTickerForValueByType($transaction) }}">
                            {{ \App\Helpers\TransactionHelper::getValueByType($transaction) }}
                        </td>
                    @else
                        <td>-</td>
                    @endif
                    <td>
                        {{ \App\Helpers\CurrencyHelper::nanoToDeso($transaction['TransactionMetadata']['BasicTransferTxindexMetadata']['FeeNanos'], 7) }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <x-transactions-pagination :$address :$page :$transactionQuantity></x-transactions-pagination>
@endsection

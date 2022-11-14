@extends('layouts.main')

@section('title', 'Transaction details')

@section('content')
    <div class="mt-3 mb-5">
        <x-search></x-search>
    </div>

    <div class="d-flex justify-content-between">
        <h1 class="h2 mb-3">Transaction details</h1>
        <div>
            @if($lastTransaction)
                <a href="{{ route('transaction', ['transactionId' => $lastTransaction]) }}"
                   class="btn btn-outline-primary">
                    Last transaction
                </a>
            @endif
        </div>
    </div>

    <div class="border">
        <div class="row m-2 mb-3">
            <div class="col-3">Transaction ID:</div>
            <div class="col-9">{{ $transaction['TransactionIDBase58Check'] }}</div>
        </div>
        <div class="row m-2 mb-3">
            <div class="col-3">Transaction Type:</div>
            <div class="col-9">{{ \App\Helpers\StringHelper::formatTransactionType($transaction['TransactionType']) }}</div>
        </div>
        <div class="row m-2 mb-3">
            <div class="col-3">Block:</div>
            <div class="col-9">
                <a href="{{ route('block', ['block' => $block['Height']]) }}">{{ $block['Height'] }}</a>
            </div>
        </div>
        <div class="row m-2 mb-3">
            <div class="col-3">Timestamp:</div>
            <div class="col-9">
                {{ \Carbon\Carbon::createFromTimestamp($block['TstampSecs'])->diffForHumans() }}
                ({{ \Carbon\Carbon::createFromTimestamp($block['TstampSecs'])->toDateTimeString() }})
            </div>
        </div>
        <div class="border-bottom"></div>
        <div class="row m-2 mb-3">
            <div class="col-3">From:</div>
            <div class="col-9">
                @if(count($transaction['TransactionMetadata']['TxnOutputs']) === 1)
                    <a href="{{ route('address', ['address' => $transaction['Outputs'][0]['PublicKeyBase58Check']]) }}">
                        {{ $transaction['Outputs'][0]['PublicKeyBase58Check'] }}
                    </a>
                @else
                    <a href="{{ route('address', ['address' => $transaction['Outputs'][1]['PublicKeyBase58Check']]) }}">
                        {{ $transaction['Outputs'][1]['PublicKeyBase58Check'] }}
                    </a>
                @endif
            </div>
        </div>
        <div class="row m-2 mb-3">
            <div class="col-3">To:</div>
            <div class="col-9">
                @if(isset($transaction['Outputs'][1]['PublicKeyBase58Check']))
                    <a href="{{ route('address', ['address' => $transaction['Outputs'][0]['PublicKeyBase58Check']]) }}">
                        {{ $transaction['Outputs'][0]['PublicKeyBase58Check'] }}
                    </a>
                @else
                    -
                @endif
            </div>
        </div>
        <div class="border-bottom"></div>
        <div class="row m-2 mb-3">
            <div class="col-3">Value:</div>
            <div class="col-9">
                {{ \App\Helpers\CurrencyHelper::nanoToDeso($transaction['Outputs'][0]['AmountNanos'], null) }} DESO
                (${{ \App\Helpers\CurrencyHelper::nanoToDollars($transaction['Outputs'][0]['AmountNanos'], $desoDesoPrice, 8) }})
            </div>
        </div>
        <div class="row m-2 mb-3">
            <div class="col-3">Transaction Fee:</div>
            <div class="col-9">
                {{ \App\Helpers\CurrencyHelper::nanoToDeso($transaction['TransactionMetadata']['BasicTransferTxindexMetadata']['FeeNanos'], 7) }} DESO
                (${{ \App\Helpers\CurrencyHelper::nanoToDollars($transaction['TransactionMetadata']['BasicTransferTxindexMetadata']['FeeNanos'], $desoDesoPrice, 8) }})
            </div>
        </div>
    </div>
    </div>
@endsection

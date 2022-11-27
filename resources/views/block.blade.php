@extends('layouts.main')

@section('title', 'Transaction details')

@section('content')
    <div class="mt-3 mb-5">
        <x-search></x-search>
    </div>

    <div class="d-flex justify-content-between">
        <h1 class="h2 mb-3">Block details</h1>
        <div>
{{--            @if(isset($block['PrevBlockHashHex']))--}}
{{--                <a href="{{ route('block', ['block' => $block['PrevBlockHashHex']]) }}"--}}
{{--                   class="btn btn-outline-primary">--}}
{{--                    Previous block--}}
{{--                </a>--}}
{{--            @endif--}}
        </div>
    </div>

    <div class="border info-table">
        <div class="row m-2 mb-3">
            <div class="col-md-3">Block hash hex:</div>
            <div class="col-md-9 text-break">{{ $block['BlockHashHex'] }}</div>
        </div>
        <div class="row m-2 mb-3">
            <div class="col-md-3">Height:</div>
            <div class="col-md-9 text-break">{{ $block['Height'] }}</div>
        </div>
        <div class="row m-2 mb-3">
            <div class="col-md-3">Version:</div>
            <div class="col-md-9 text-break">{{ $block['Version'] }}</div>
        </div>
        <div class="row m-2 mb-3">
            <div class="col-md-3">Timestamp:</div>
            <div id="timestamp" class="col-md-9 text-break text-break" data-timestamp="{{ $block['TstampSecs'] ?? '' }}">
                @if(isset($block['TstampSecs']))
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                @else
                    Pending...
                @endif
            </div>
        </div>
        <a class="row m-2 mb-2" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
            <div class="col-md-3">
                Show more...
            </div>
        </a>
        <div class="collapse" id="collapseExample">
            <div class="row m-2 mb-3">
                <div class="col-md-3">Previous block hash hex:</div>
                <div class="col-md-9 text-break">{{ $block['PrevBlockHashHex'] }}</div>
            </div>
            <div class="row m-2 mb-3">
                <div class="col-md-3">Transaction merkle root hex:</div>
                <div class="col-md-9 text-break">{{ $block['PrevBlockHashHex'] }}</div>
            </div>
            <div class="row m-2 mb-3">
                <div class="col-md-3">Nonce:</div>
                <div class="col-md-9 text-break">{{ $block['Nonce'] }}</div>
            </div>
            <div class="row m-2 mb-3">
                <div class="col-md-3">ExtraNonce:</div>
                <div class="col-md-9 text-break">{{ $block['ExtraNonce'] }}</div>
            </div>
        </div>
    </div>
    <x-transactions :$transactions></x-transactions>
@endsection

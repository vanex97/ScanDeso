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
            <div class="col-3">Block hash hex:</div>
            <div class="col-9">{{ $block['BlockHashHex'] }}</div>
        </div>
        <div class="row m-2 mb-3">
            <div class="col-3">Height:</div>
            <div class="col-9">{{ $block['Height'] }}</div>
        </div>
        <div class="row m-2 mb-3">
            <div class="col-3">Version:</div>
            <div class="col-9">{{ $block['Version'] }}</div>
        </div>
        <div class="row m-2 mb-3">
            <div class="col-3">Timestamp:</div>
            <div id="timestamp" class="col-9" data-timestamp="{{ $block['TstampSecs'] ?? '' }}">
                @if(isset($block['TstampSecs']))
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                @else
                    Pending...
                @endif
            </div>
        </div>
    </div>
    </div>
@endsection

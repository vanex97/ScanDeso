@extends('layouts.main')

@section('title', 'Transaction details')

@section('content')
    <div class="mt-3 mb-5">
        <x-search></x-search>
    </div>

    <div class="d-flex justify-content-between">
        <h1 class="h2 mb-3">DeSo parsing status</h1>
    </div>

    <div class="border info-table">
        <div class="row m-2 mb-3">
            <div class="col-md-3">Last parsed block:</div>
            <div class="col-md-9 text-break">
                <a href="{{ route('block', ['block' => $lastParsedBlock]) }}">{{ $lastParsedBlock }}</a>
            </div>
        </div>
        <div class="row m-2 mb-3">
            <div class="col-md-3">Usernames parsed:</div>
            <div class="col-md-9 text-break">{{ number_format($userNamesParsed) }}</div>
        </div>
@endsection

@extends('layouts.main')
@section('title', '404')

@section('content')
    <div class="mt-3">
        <x-search></x-search>
    </div>

    <div class="main-search-home text-center">
        <h1>404</h1>
        <p class="text-muted">
            @if($exception->getMessage() != null)
                {{ $exception->getMessage() }}
            @else
                Page not found
            @endif
        </p>
    </div>
@endsection


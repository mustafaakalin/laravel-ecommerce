
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="text-center">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">Oops! Something went wrong</h1>
        @if(config('app.debug'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Error:</strong>
                <span class="block sm:inline">{{ $error }}</span>
            </div>
        @endif
        <div class="mt-6">
            <a href="{{ route('home') }}" class="btn btn-primary">Return Home</a>
        </div>
    </div>
</div>
@endsection
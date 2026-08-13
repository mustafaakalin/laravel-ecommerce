@extends('layouts.app')

@section('title', 'Yeni Adres')

@section('content')
    <div class="container mx-auto py-8 px-4 max-w-3xl">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">
                <i class="fa-solid fa-plus"></i>
                Yeni Adres
            </h1>
            <a href="{{ route('profile.addresses.index') }}" class="btn btn-ghost btn-sm">
                <i class="fa-solid fa-arrow-left"></i>
                Adreslerime Dön
            </a>
        </div>

        @include('profile.addresses.form', [
            'address' => null,
            'action' => route('profile.addresses.store'),
            'method' => 'POST',
        ])
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Adres Düzenle')

@section('content')
    <div class="container mx-auto py-8 px-4 max-w-3xl">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">
                <i class="fa-solid fa-pen"></i>
                Adres Düzenle
            </h1>
            <a href="{{ route('profile.addresses.index') }}" class="btn btn-ghost btn-sm">
                <i class="fa-solid fa-arrow-left"></i>
                Adreslerime Dön
            </a>
        </div>

        @include('profile.addresses.form', [
            'address' => $address,
            'action' => route('profile.addresses.update', $address),
            'method' => 'PUT',
        ])
    </div>
@endsection

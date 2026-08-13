@extends('layouts.app')

@section('content')
<div class="min-h-screen ">
    <!-- Hero Section -->
    <div class="hero min-h-[40vh]">
        <div class="hero-content text-center">
            <div class="max-w-3xl">
                <h1 class="text-5xl font-bold mb-8">Sıkça Sorulan Sorular</h1>
                <div class="divider"></div>
            </div>
        </div>
    </div>

    <!-- FAQ Content -->
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-3xl mx-auto space-y-4">
            @foreach($faqs as $faq)
                <div class="collapse collapse-arrow  shadow-lg">
                    <input type="checkbox" class="peer" /> 
                    <div class="collapse-title text-xl font-medium peer-checked:bg-primary/10">
                        {{ $faq['question'] }}
                    </div>
                    <div class="collapse-content peer-checked:bg-primary/5"> 
                        <div class="py-4 prose">
                            {!! Str::markdown($faq['answer']) !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
<div class="flex space-x-2">
    @if($getRecord()->social_instagram)
        <a href="{{ $getRecord()->social_instagram }}" target="_blank" class="text-pink-600 hover:text-pink-700">
            <x-heroicon-m-camera class="w-5 h-5" />
        </a>
    @endif

    @if($getRecord()->social_facebook)
        <a href="{{ $getRecord()->social_facebook }}" target="_blank" class="text-blue-600 hover:text-blue-700">
            <x-heroicon-m-face-smile class="w-5 h-5" />
        </a>
    @endif

    @if($getRecord()->social_youtube)
        <a href="{{ $getRecord()->social_youtube }}" target="_blank" class="text-red-600 hover:text-red-700">
            <x-heroicon-m-play class="w-5 h-5" />
        </a>
    @endif

    @if($getRecord()->social_tiktok)
        <a href="{{ $getRecord()->social_tiktok }}" target="_blank" class="text-gray-900 hover:text-gray-700">
            <x-heroicon-m-musical-note class="w-5 h-5" />
        </a>
    @endif

    @if($getRecord()->social_linkedin)
        <a href="{{ $getRecord()->social_linkedin }}" target="_blank" class="text-blue-700 hover:text-blue-800">
            <x-heroicon-m-briefcase class="w-5 h-5" />
        </a>
    @endif

    @if($getRecord()->social_x)
        <a href="{{ $getRecord()->social_x }}" target="_blank" class="text-gray-800 hover:text-gray-900">
            <x-heroicon-m-chat-bubble-bottom-center-text class="w-5 h-5" />
        </a>
    @endif
</div>
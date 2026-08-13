<!-- filepath: /resources/views/livewire/comment-component.blade.php -->
<div class="mt-8 md:mt-16">
    <div class="collapse collapse-arrow  backdrop-blur-sm rounded-lg">
        <input type="checkbox" defaultChecked />
        <div class="collapse-title flex items-center gap-2">
            <h2 class="text-xl md:text-2xl font-bold flex items-center gap-2">
                <i class="fas fa-comments text-primary text-lg md:text-2xl"></i>
                Ürün Yorumları
                @if ($comments->count() > 0)
                    <span class="text-primary text-sm md:text-base">({{ $comments->count() }})</span>
                @endif
            </h2>
        </div>
        <div class="collapse-content">

            <div class="space-y-4 md:space-y-6">
                @auth
                    <form wire:submit="addComment" class="mb-4 md:mb-8">
                        <div class="card  backdrop-blur-sm shadow-lg">
                            <div class="card-body p-4 md:p-6">
                                <div class="flex flex-col md:flex-row gap-4">
                                    <div class="avatar self-center md:self-start">
                                        <div class="w-10 h-10 md:w-12 md:h-12 rounded-full ring-2 md:ring-4 ring-primary">
                                            <img src="{{ auth()->user()->avatar ? '/storage/' . auth()->user()->avatar : '/default_user_avatar.jpg' }}"
                                                 alt="{{ auth()->user()->name }}" />
                                        </div>
                                    </div>
                                    <div class="flex-1 space-y-3 md:space-y-4">
                                        <div class="form-control">
                                            <label class="label">
                                                <span class="label-text font-semibold">Puanınız</span>
                                            </label>
                                            <div class="rating rating-md md:rating-lg">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <input type="radio" wire:model="rating" value="{{ $i }}"
                                                        class="mask mask-star-2 bg-orange-400" />
                                                @endfor
                                            </div>
                                            @error('rating')
                                                <span class="text-error text-xs md:text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-control">
                                            <textarea wire:model="content"
                                                class="textarea textarea-bordered min-h-[100px] md:min-h-[120px] focus:textarea-primary text-sm md:text-base"
                                                placeholder="Bu ürün hakkında ne düşünüyorsunuz?"></textarea>
                                            @error('content')
                                                <span class="text-error text-xs md:text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="flex justify-end">
                                            <button type="submit" class="btn btn-primary btn-sm md:btn-md gap-2">
                                                <i class="fas fa-paper-plane"></i>
                                                Yorumu Gönder
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="alert alert-info shadow-lg mb-4 md:mb-8 text-sm md:text-base">
                        <i class="fas fa-info-circle text-lg md:text-xl"></i>
                        <div>
                            <span>Yorum yapabilmek için lütfen </span>
                            <a href="{{ route('filament.admin.auth.login') }}"
                                class="font-bold hover:text-primary transition-colors">giriş yapın</a>.
                        </div>
                    </div>
                @endauth

                <div wire:loading.delay class="w-full flex justify-center">
                    <span class="loading loading-spinner loading-md md:loading-lg text-primary"></span>
                </div>

                <div wire:loading.remove>
                    @forelse($comments as $comment)
                        <div class="card backdrop-blur-sm hover:bg-base-300 shadow-lg hover:shadow-2xl transition-all duration-300 m-2 md:m-3"
                            wire:key="comment-{{ $comment->id }}">
                            <div class="card-body p-3 md:p-6">
                                <div class="flex flex-col md:flex-row gap-3 md:gap-4">
                                    <div class="avatar self-center md:self-start">
                                        <div
                                            class="w-10 h-10 md:w-12 md:h-12 rounded-full ring-2 md:ring-4 ring-primary">
                                            <img src="{{ $comment->user->avatar ? '/storage/' . $comment->user->avatar : '/default_user_avatar.jpg' }}"
                                                alt="{{ $comment->user->name }}" />
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex flex-col md:flex-row md:items-center justify-between mb-2">
                                            <div>
                                                <h4 class="font-bold text-base md:text-lg text-center md:text-left">
                                                    {{ $comment->user->name }}</h4>
                                                <div class="flex flex-col md:flex-row items-center gap-2 md:gap-4">
                                                    <div class="rating rating-xs md:rating-sm">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <input type="radio" class="mask mask-star-2 bg-orange-400"
                                                                @if ($i === $comment->rating) checked @endif
                                                                disabled />
                                                        @endfor
                                                    </div>
                                                    <span class="text-xs md:text-sm text-base-content/60">
                                                        {{ $comment->created_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                            </div>

                                            @if (auth()->id() === $comment->user_id)
                                                <button wire:click="deleteComment({{ $comment->id }})"
                                                    wire:confirm="Yorumu silmek istediğinize emin misiniz?"
                                                    class="btn btn-ghost btn-xs md:btn-sm text-error">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            @endif
                                        </div>
                                        <p class="text-base-content/80 mt-2 text-sm md:text-base">
                                            {{ $comment->content }}</p>
                                    </div>
                                </div>

                                @if (isset($comment->user->instagram_account) ||
                                        isset($comment->user->facebook_account) ||
                                        isset($comment->user->x_account) ||
                                        isset($comment->user->tiktok_account))
                                    <div class="flex items-center justify-center mt-3 md:mt-4 space-x-3 md:space-x-4">
                                        @if (isset($comment->user->instagram_account))
                                            <a href="https://www.instagram.com/{{ $comment->user->instagram_account }}"
                                                target="_blank" rel="noopener noreferrer"
                                                class="text-gray-500 hover:text-gray-700">
                                                <i class="fab fa-instagram text-lg md:text-2xl"></i>
                                            </a>
                                        @endif
                                        @if (isset($comment->user->facebook_account))
                                            <a href="https://www.facebook.com/{{ $comment->user->facebook_account }}"
                                                target="_blank" rel="noopener noreferrer"
                                                class="text-gray-500 hover:text-gray-700">
                                                <i class="fab fa-facebook text-lg md:text-2xl"></i>
                                            </a>
                                        @endif
                                        @if (isset($comment->user->x_account))
                                            <a href="https://x.com/{{ $comment->user->x_account }}" target="_blank"
                                                rel="noopener noreferrer" class="text-gray-500 hover:text-gray-700">
                                                <i class="fab fa-x-twitter text-lg md:text-2xl"></i>
                                            </a>
                                        @endif
                                        @if (isset($comment->user->tiktok_account))
                                            <a href="https://www.tiktok.com/{{ '@' . $comment->user->tiktok_account }}"
                                                target="_blank" rel="noopener noreferrer"
                                                class="text-gray-500 hover:text-gray-700">
                                                <i class="fab fa-tiktok text-lg md:text-2xl"></i>
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 md:py-12">
                            <div class="avatar placeholder mb-3 md:mb-4">
                                <div class="bg-neutral text-neutral-content rounded-full w-16 md:w-24">
                                    <span class="text-2xl md:text-3xl">?</span>
                                </div>
                            </div>
                            <h3 class="text-lg md:text-xl font-bold mb-2">Henüz yorum yapılmamış</h3>
                            <p class="text-sm md:text-base text-base-content/60">İlk yorumu siz yapın ve diğer
                                kullanıcılara
                                yardımcı olun!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

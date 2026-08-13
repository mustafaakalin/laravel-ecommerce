{{-- resources/views/livewire/quick-view-button.blade.php --}}
<div class="inline-block">
    {{-- Quick View Button --}}
    <button wire:click="loadProduct" class="" aria-label="Hızlı Görünüm">
        {{-- <i class="fas fa-eye w-4 h-4"></i> --}}
    </button>

    {{-- Quick View Modal --}}
    @if ($show && $product)
        <div class="fixed inset-0 z-30 overflow-y-auto">
            {{-- Modal Backdrop --}}
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-all duration-300" wire:click="closeModal"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            </div>

            {{-- Modal Container --}}
            <div class="flex min-h-full items-center justify-center p-2 sm:p-4">
                <div class="relative w-full max-w-4xl bg-base-100 rounded-lg shadow-2xl overflow-hidden"
                    x-data="{ activeTab: 'details' }" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                    {{-- Close Button --}}
                    <button wire:click="closeModal"
                        class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 z-50 hover:rotate-90 transition-transform duration-200">
                        <i class="fas fa-times"></i>
                    </button>

                    <div class="grid grid-cols-1 lg:grid-cols-2 h-full max-h-[90vh] lg:max-h-[80vh]">
                        {{-- Left Column - Image Carousel --}}
                        <div class="relative p-4 sm:p-6 bg-base-200">
                            {{-- Status Badges --}}
                            <div class="absolute top-6 left-6 flex flex-col gap-2 z-10">
                                @if ($product->old_price)
                                    <div class="badge badge-lg badge-secondary gap-2 font-semibold">
                                        <i class="fas fa-clock w-5 h-5"></i>
                                        {{ number_format((($product->old_price - $product->price) / $product->old_price) * 100, 0) }}%
                                        İNDİRİM
                                    </div>
                                @endif
                                @if ($product->is_new)
                                    <div class="badge badge-lg badge-primary">YENİ ÜRÜN</div>
                                @endif
                            </div>

                            {{-- Image Carousel --}}
                            <div class="carousel w-full h-[250px] sm:h-[350px] lg:h-[400px] rounded-lg">
                                @forelse($product->images as $index => $image)
                                    <div id="quick-view-slide{{ $index }}" class="carousel-item relative w-full">
                                        <img src="{{ Storage::url($image->image_path) }}"
                                            alt="{{ $product->name }} - Görsel {{ $index + 1 }}"
                                            class="w-full h-full object-contain">

                                        @if (count($product->images) > 1)
                                            <div
                                                class="absolute flex justify-between transform -translate-y-1/2 left-2 right-2 top-1/2">
                                                <a href="#quick-view-slide{{ $index - 1 < 0 ? count($product->images) - 1 : $index - 1 }}"
                                                    class="btn btn-circle btn-sm bg-base-100/50 hover:bg-base-100"><i class="fas fa-chevron-left"></i></a>
                                                <a href="#quick-view-slide{{ $index + 1 >= count($product->images) ? 0 : $index + 1 }}"
                                                    class="btn btn-circle btn-sm bg-base-100/50 hover:bg-base-100"><i class="fas fa-chevron-right"></i></a>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="w-full h-full flex items-center justify-center bg-base-300">
                                        <img src="/default_product_image.jpg" alt="{{ $product->name }}"
                                            class="w-40 h-40 object-contain opacity-50">
                                    </div>
                                @endforelse
                            </div>

                            {{-- Thumbnail Navigation --}}
                            @if (count($product->images) > 1)
                                <div class="hidden sm:flex justify-center gap-2 mt-4 px-2">
                                    @foreach ($product->images as $index => $image)
                                        <a href="#quick-view-slide{{ $index }}"
                                            class="w-16 h-16 rounded-lg overflow-hidden border-2 hover:border-primary transition-colors duration-200"
                                            :class="{
                                                'border-primary': window.location
                                                    .hash === '#quick-view-slide{{ $index }}',
                                                'border-base-300': window
                                                    .location.hash !== '#quick-view-slide{{ $index }}'
                                            }">
                                            <img src="{{ Storage::url($image->image_path) }}"
                                                alt="{{ $product->name }} - Küçük Görsel {{ $index + 1 }}"
                                                class="w-full h-full object-cover">
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Right Column - Product Details --}}
                        <div class="flex flex-col h-full overflow-hidden">
                            <div class="p-4 sm:p-6 flex-1 overflow-y-auto">
                                {{-- Product Title & Price --}}
                                <div class="mb-6">
                                    <h2 class="text-xl sm:text-2xl font-bold mb-2">{{ $product->name }}</h2>
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="text-xl sm:text-2xl font-bold text-primary">{{ number_format($product->price, 2, ',', '.') }}
                                            TL</span>
                                        @if ($product->old_price)
                                            <span class="text-base sm:text-lg line-through text-base-content/60">
                                                {{ number_format($product->old_price, 2, ',', '.') }} TL
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Product Rating --}}
                                <div class="flex items-center gap-4 my-6">
                                    {{-- Star Icons --}}
                                    <div class="flex items-center gap-1">
                                        <span class="text-lg font-semibold"></span>
                                        <div class="rating">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <input type="radio" name="rating-{{ $product->id }}"
                                                    class="mask mask-star-2 bg-yellow-400"
                                                    {{ $i <= $product->rating ? 'checked' : '' }} disabled />
                                            @endfor
                                        </div>
                                    </div>
                                    {{-- Numerical Rating --}}
                                    <span class="text-lg font-semibold text-gray-700">{{ $product->rating }}</span>
                                </div>

                                {{-- Tabs Navigation --}}
                                <div class="tabs tabs-boxed bg-base-200/50 p-1 mb-4">
                                    <button class="tab flex-1 text-sm sm:text-base"
                                        :class="{ 'tab-active': activeTab === 'details' }"
                                        @click="activeTab = 'details'">
                                        Ürün Detayları
                                    </button>
                                    <button class="tab flex-1 text-sm sm:text-base"
                                        :class="{ 'tab-active': activeTab === 'specs' }"
                                        @click="activeTab = 'specs'">
                                        Özellikler
                                    </button>
                                </div>

                                {{-- Tab Contents --}}
                                <div class="space-y-4">
                                    {{-- Details Tab --}}
                                    <div x-show="activeTab === 'details'"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0" 
                                        x-transition:enter-end="opacity-100">
                                        <div class="prose prose-sm break-words sm:prose max-w-none max-h-[200px] overflow-y-auto scrollbar-thin scrollbar-thumb-base-300 scrollbar-track-base-100 p-2">
                                            {!! $product->description !!}
                                        </div>
                                    </div>

                                    {{-- Specifications Tab --}}
                                    <div x-show="activeTab === 'specs'"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0" 
                                        x-transition:enter-end="opacity-100">
                                        {{-- Specifications Tab --}}
                                        @if ($product->specifications)
                                            <div class="max-h-[200px] overflow-y-auto scrollbar-thin scrollbar-thumb-base-300 scrollbar-track-base-100">
                                                <div class="overflow-x-auto w-full">
                                                    <table class="table table-zebra w-full">
                                                        <thead class="sticky top-0 z-10">
                                                            <tr>
                                                                <th class="bg-base-200">Özellik</th>
                                                                <th class="bg-base-200">Değer</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($product->specifications as $key => $value)
                                                                <tr class="hover:bg-base-200 transition-all duration-200">
                                                                    <td class="font-medium">{{ $key }}</td>
                                                                    <td>{{ $value }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle w-6 h-6"></i>
                                                <span>Bu ürün için özellik bilgisi girilmemiş.</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            </div>

                            {{-- Footer Actions --}}
                            <div class="p-4 sm:p-6 bg-base-200/50 border-t border-base-300">
                                {{-- Stock Status --}}
                                <div class="flex items-center gap-3 mb-4">
                                    <span class="font-medium">Stok Durumu:</span>
                                    @if ($product->stock > 0)
                                        <span class="badge badge-success gap-2">
                                            <span class="relative flex h-2 w-2">
                                                <span
                                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-success-content opacity-75"></span>
                                                <span
                                                    class="relative inline-flex rounded-full h-2 w-2 bg-success-content"></span>
                                            </span>
                                            Stokta var ({{ $product->stock }})
                                        </span>
                                    @else
                                        <span class="badge badge-error gap-2">Stokta yok</span>
                                    @endif
                                </div>

                                {{-- Action Buttons --}}
                                <div class="flex flex-col sm:flex-row gap-3">
                                    @if ($product->stock > 0)
                                        <livewire:add-to-cart :product="$product" :button-classes="'btn btn-primary flex-1 normal-case gap-2 hover:scale-105 transition-transform duration-200'"
                                            :wire:key="'add-to-cart-'.$product->id" />
                                    @endif

                                    <a href="{{ route('products.show', $product->slug) }}"
                                        class="btn btn-outline btn-secondary flex-1 hover:scale-105 transition-transform duration-200">
                                        Detayları Gör
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

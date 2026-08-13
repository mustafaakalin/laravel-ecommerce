{{-- resources/livewire/product-listing.blade.php --}}
<div class="min-h-screen">

    <!-- Breadcrumbs -->
    <div class="mb-8">
        <div class="text-sm breadcrumbs">
            <ul>
                <li>
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <i class="fas fa-home h-4 w-4"></i>
                        Anasayfa
                    </a>
                </li>
                <li>
                    <a href="{{ route('products.index') }}" class="flex items-center gap-2">
                        <i class="fas fa-store h-4 w-4"></i>
                        Ürünler
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <!-- Arama ve Sıralama -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <!-- Arama Çubuğu -->
            <div class="w-full md:w-1/3 mb-4 md:mb-0">
                <input type="text" wire:model.live.debounce.500ms="search" placeholder="Ürün Ara"
                    class="input input-bordered w-full" />
            </div>
            <!-- Sıralama Seçenekleri -->
            <div class="w-full md:w-1/3 flex justify-end">
                <select wire:model.live="sort" class="select select-bordered w-full md:w-48">
                    <option value="newest">Yeni Eklenenler</option>
                    <option value="price_asc">Fiyata Göre Artan</option>
                    <option value="price_desc">Fiyata Göre Azalan</option>
                </select>
            </div>
        </div>

        <!-- Filtreler ve Ürün Listesi -->
        <div class="flex flex-col lg:flex-row mb-6">





            <!-- Loading State -->
            @if ($isLoading)
                <div class="flex justify-center items-center py-8">
                    <span class="loading loading-spinner loading-lg"></span>
                </div>
            @else
                <!-- Main Content -->
                <div class="flex flex-col lg:flex-row gap-6">
                    <!-- Filters -->
                    <!-- Filtreler -->
                    <div class="w-full lg:w-1/4 mb-4 lg:mb-0 lg:mr-4">
                        <div x-data="{ open: false }" class="lg:hidden">
                            <button @click="open = !open" class="btn btn-primary w-full mb-4">Filtreleri Göster</button>

                            <div x-show="open" x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                class="p-4 rounded shadow-md backdrop-blur-sm">
                                <!-- Kategoriler -->
                                <div class="mb-4">
                                    <h2 class="text-lg font-semibold mb-2">Kategoriler</h2>
                                    <div class=" max-h-48 overflow-y-auto">
                                        @foreach ($categories as $category)
                                            <div class="form-control">
                                                <label class="cursor-pointer label">
                                                    <input type="checkbox"
                                                        wire:model.live.debounce.500ms="tempFilters.categories"
                                                        value="{{ $category->id }}" class="checkbox checkbox-primary" />
                                                    <span class="label-text ml-2">{{ $category->name }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Markalar -->
                                <div class="mb-4">
                                    <h2 class="text-lg font-semibold mb-2">Markalar</h2>
                                    <div class=" max-h-48 overflow-y-auto">
                                        @foreach ($brands as $brand)
                                            <div class="form-control">
                                                <label class="cursor-pointer label">
                                                    <input type="checkbox"
                                                        wire:model.live.debounce.500ms="tempFilters.brands"
                                                        value="{{ $brand->id }}" class="checkbox checkbox-primary" />
                                                    <span class="label-text ml-2">{{ $brand->name }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Fiyat Aralığı -->
                                <div class="mb-4">
                                    <h2 class="text-lg font-semibold mb-2">Fiyat Aralığı</h2>
                                    <div class="flex space-x-2">
                                        <!-- Fiyat inputları için debounce eklenmesi -->
                                        <input type="number" wire:model.live.debounce.500ms="tempFilters.price_min"
                                            placeholder="Min" class="input input-bordered w-full" />
                                        <!-- Fiyat inputları için debounce eklenmesi -->
                                        <input type="number" wire:model.live.debounce.500ms="tempFilters.price_max"
                                            placeholder="Max" class="input input-bordered w-full" />
                                    </div>
                                </div>

                                <!-- Hızlı Filtreler -->
                                <div class="mb-4">
                                    <h2 class="text-lg font-semibold mb-2">Hızlı Filtreler</h2>
                                    <div class="form-control">
                                        <label class="cursor-pointer label">
                                            <input type="checkbox" wire:model.live.debounce.500ms="tempFilters.only_active"
                                                class="checkbox checkbox-primary" />
                                            <span class="label-text ml-2">Aktif Ürünler</span>
                                        </label>
                                        <label class="cursor-pointer label">
                                            <input type="checkbox" wire:model.live.debounce.500ms="tempFilters.only_in_stock"
                                                class="checkbox checkbox-primary" />
                                            <span class="label-text ml-2">Stokta Olan Ürünler</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <button type="button" wire:click="applyFilters" class="btn btn-primary w-full">
                                        <span wire:loading.remove wire:target="applyFilters">Filtreleri Uygula</span>
                                        <span wire:loading wire:target="applyFilters">
                                            <span class="loading loading-spinner loading-sm"></span>
                                            Uygulanıyor...
                                        </span>
                                    </button>
                                    <button type="button" wire:click="resetFilters" class="btn btn-secondary w-full">
                                        <span wire:loading.remove wire:target="resetFilters">Filtreleri Sıfırla</span>
                                        <span wire:loading wire:target="resetFilters">
                                            <span class="loading loading-spinner loading-sm"></span>
                                            Sıfırlanıyor...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="hidden lg:block sticky top-16">
                            <!-- Kategoriler -->
                            <div class="mb-4">
                                <h2 class="text-lg font-semibold mb-2">Kategoriler</h2>
                                <div class=" max-h-48 overflow-y-auto">
                                    @foreach ($categories as $category)
                                        <div class="form-control">
                                            <label class="cursor-pointer label">
                                                <input type="checkbox" wire:model.live.debounce.500ms="tempFilters.categories"
                                                    value="{{ $category->id }}" class="checkbox checkbox-primary" />
                                                <span class="label-text ml-2">{{ $category->name }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Markalar -->
                            <div class="mb-4">
                                <h2 class="text-lg font-semibold mb-2">Markalar</h2>
                                <div class=" max-h-48 overflow-y-auto">
                                    @foreach ($brands as $brand)
                                        <div class="form-control">
                                            <label class="cursor-pointer label">
                                                <input type="checkbox" wire:model.live.debounce.500ms="tempFilters.brands"
                                                    value="{{ $brand->id }}" class="checkbox checkbox-primary" />
                                                <span class="label-text ml-2">{{ $brand->name }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Fiyat Aralığı -->
                            <div class="mb-4">
                                <h2 class="text-lg font-semibold mb-2">Fiyat Aralığı</h2>
                                <div class="flex space-x-2">
                                    <input type="number" wire:model.live.debounce.500ms="tempFilters.price_min" placeholder="Min"
                                        class="input input-bordered w-full" />
                                    <input type="number" wire:model.live.debounce.500ms="tempFilters.price_max" placeholder="Max"
                                        class="input input-bordered w-full" />
                                </div>
                            </div>

                            <!-- Hızlı Filtreler -->
                            <div class="mb-4">
                                <h2 class="text-lg font-semibold mb-2">Hızlı Filtreler</h2>
                                <div class="form-control">
                                    <label class="cursor-pointer label">
                                        <input type="checkbox" wire:model.live.debounce.500ms="tempFilters.only_active"
                                            class="checkbox checkbox-primary" />
                                        <span class="label-text ml-2">Aktif Ürünler</span>
                                    </label>
                                    <label class="cursor-pointer label">
                                        <input type="checkbox" wire:model.live.debounce.500ms="tempFilters.only_in_stock"
                                            class="checkbox checkbox-primary" />
                                        <span class="label-text ml-2">Stokta Olan Ürünler</span>
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <button type="button" wire:click="applyFilters" class="btn btn-primary w-full">
                                    <span wire:loading.remove wire:target="applyFilters">Filtreleri Uygula</span>
                                    <span wire:loading wire:target="applyFilters">
                                        <span class="loading loading-spinner loading-sm"></span>
                                        Uygulanıyor...
                                    </span>
                                </button>
                                <button type="button" wire:click="resetFilters" class="btn btn-secondary w-full">
                                    <span wire:loading.remove wire:target="resetFilters">Filtreleri Sıfırla</span>
                                    <span wire:loading wire:target="resetFilters">
                                        <span class="loading loading-spinner loading-sm"></span>
                                        Sıfırlanıyor...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Product Grid -->
                    <div class="w-full lg:w-3/4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @forelse($products as $product)
                                <x-product-card :product="$product" wire:key="product-{{ $product->id }}" />
                            @empty
                                <div class="col-span-full text-center py-8">
                                    <p class="text-gray-500">Ürün bulunamadı.</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Sayfalama -->
                        {{-- <div wire:loading.remove class="mt-6">
                            {{ $products->links() }}
                        </div> --}}
                    </div>
                </div>
            @endif



        </div>
    </div>

    @script
        <script>
            // Handle modal close after filter changes
            document.addEventListener('livewire:initialized', () => {
                const modal = document.getElementById('mobile_filters')

                @this.on('filters-updated', () => {
                    setTimeout(() => {
                        modal.close()
                    }, 300)
                })
            })
        </script>
    @endscript
    @script
        <script>
            document.addEventListener('livewire:initialized', () => {
                // filters-updated eventi dinleniyor
                Livewire.on('filters-updated', () => {
                    // Alpine.js state'ini kontrol et
                    const filterMenu = document.querySelector('[x-data]')?.__x;
                    if (filterMenu) {
                        filterMenu.getScope().open = false;
                    }
                });
            });
        </script>
    @endscript
</div>

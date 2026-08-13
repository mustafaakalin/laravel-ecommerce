<!-- Ana Dropdown Container -->
<div class="dropdown dropdown-bottom   w-full ">
    <!-- Arama Input ve Tetikleyici -->
    <div class="form-control w-full " tabindex="0">
        <div class="relative">
            <!-- Arama İkonu -->
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-base-content/60">
                <i class="fas fa-search h-4 w-4"></i>
            </span>
            
            <!-- Arama Input -->
            <input type="text" 
                wire:model.live.debounce.300ms="search" 
                placeholder="Ürün ara..." 
                class="input input-bordered w-full pl-10 pr-4 h-9 text-sm bg-gradient-to-r from-primary/10 to-secondary/10 bg-transparent font-bold text-base-content"
            />
            
            <!-- Loading Spinner -->
            <div wire:loading class="absolute inset-y-0 right-0 flex items-center pr-3">
                <span class="loading loading-spinner loading-xs text-base-content/60"></span>
            </div>
        </div>
    </div>

    <!-- Dropdown İçeriği -->
    @if($isOpen)
    <div tabindex="0" class="dropdown-content w-auto lg:w-[250px] backdrop-blur-md shadow-xl card card-compact mt-6 bg-gradient-to-r from-primary/10 to-secondary/10 ">
        <div class="card-body">
            @if($this->searchResults->isEmpty())
            <!-- Sonuç Bulunamadı -->
            <div class="text-center py-8">
                <i class="far fa-frown h-12 w-12 mx-auto mb-2 opacity-50 text-5xl"></i>
                <p class="text-base-content/60">Sonuç bulunamadı.</p>
            </div>
            @else
            <!-- Sonuç Listesi -->
            <div class="max-h-[60vh] overflow-y-auto">
                @foreach($this->searchResults as $product)
                <a href="{{ route('products.show', $product['slug']) }}" 
                   class="flex gap-4 p-3 hover:bg-base-200 rounded-lg transition-all">
                    <!-- Ürün Görseli -->
                    <div class="flex-none">
                        @if(isset($product['images']) && count($product['images']) > 0)
                        <div class="avatar">
                            <div class="w-16 h-16 rounded-lg">
                                <img src="/storage/{{ $product['images'][0] }}" 
                                     alt="{{ $product['name'] }}"
                                     class="object-cover" 
                                />
                            </div>
                        </div>
                        @else
                        <div class="avatar">
                            <div class="w-16 h-16 rounded-lg">
                                <img src="{{ asset('default_product_image.jpg') }}" alt="Default Product" class="object-cover">
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Ürün Detayları -->
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-base-content">
                            {!! $product['name'] !!}
                        </div>
                        
                        <div class="badge badge-primary mt-1 font-semibold">
                            {{ number_format($product['price'], 2) }} ₺
                        </div>
                        
                        <p class="text-xs text-base-content/60 line-clamp-2 mt-2">
                            {!! $product['description'] !!}
                        </p>
                        
                        @if(isset($product['tags']) && count($product['tags']) > 0)
                        <div class="flex flex-wrap gap-1 mt-2">
                            @foreach($product['tags'] as $tag)
                            <div class="badge badge-ghost badge-sm">{!! $tag !!}</div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </a>
                @if(!$loop->last)
                    <div class="divider my-1"></div>
                @endif
                @endforeach
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
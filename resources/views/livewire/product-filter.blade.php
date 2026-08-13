<div class="container mx-auto p-4">
    <div class="flex flex-wrap justify-between items-center mb-4">
        <input wire:model.live="search" type="text" placeholder="Search products..." class="input input-bordered w-full md:w-auto mb-2 md:mb-0">
        <select wire:model.live="category" class="select select-bordered w-full md:w-auto mb-2 md:mb-0">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
        <input wire:model.live="minPrice" type="number" placeholder="Min Price" class="input input-bordered w-full md:w-auto mb-2 md:mb-0">
        <input wire:model.live="maxPrice" type="number" placeholder="Max Price" class="input input-bordered w-full md:w-auto mb-2 md:mb-0">
        <select wire:model.live="sort" class="select select-bordered w-full md:w-auto mb-2 md:mb-0">
            <option value="newest">Newest</option>
            <option value="price_asc">Price: Low to High</option>
            <option value="price_desc">Price: High to Low</option>
            <option value="rating">Rating</option>
        </select>
        
    </div>

    @if($isFilterModalOpen)
        <div class="modal modal-open">
            <div class="modal-box">
                <!-- Modal içeriği buraya gelecek -->
                <h3 class="font-bold text-lg">Filter Modal</h3>
                <p class="py-4">This is a filter modal.</p>
                <div class="modal-action">
                    <button wire:click="toggleFilterModal" class="btn">Close</button>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($products as $product)
            <div class="card card-compact bg-base-100 shadow-xl">
                @if(isset($product['images']) && count($product['images']) > 0)
                    <figure><img src="/storage/{{ $product['images'][0] }}" alt="{{ $product['name'] }}" class="w-full h-48 object-cover"></figure>
                @else
                    <figure><div class="w-full h-48 bg-gray-200 flex items-center justify-center"><span class="text-3xl">?</span></div></figure>
                @endif
                <div class="card-body">
                    <h2 class="card-title">{{ $product['name'] }}</h2>
                    <p class="text-sm text-gray-500">{{ $product['description'] }}</p>
                    <div class="badge badge-primary">{{ number_format($product['price'], 2) }} TL</div>
                    <div class="card-actions justify-end">
                        <a href="{{ route('products.show', $product['slug']) }}" class="btn btn-primary">View Product</a>
                    </div>
                </div>
            </div>
            
        @endforeach
    </div>

    <div class="mt-4">
        {{-- {{ $products->links() }} --}}
    </div>
</div>
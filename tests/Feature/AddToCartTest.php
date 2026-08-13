<?php

use App\Livewire\AddToCart;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
use App\Models\Brand;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $category = Category::create(['name' => 'Test Category', 'slug' => 'test-category']);
    $brand = Brand::create(['name' => 'Test Brand', 'slug' => 'test-brand']);
    $this->product = Product::create([
        'name' => 'Test Product',
        'slug' => 'test-product',
        'description' => 'Test description',
        'price' => 100,
        'stock' => 10,
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'sku' => 'TEST-SKU-' . uniqid(),
        'is_active' => true,
    ]);
});

it('adds a product to the cart without SQL errors', function () {
    Livewire::actingAs($this->user)
        ->test(AddToCart::class, ['product' => $this->product])
        ->call('addToCart')
        ->assertDispatched('cartUpdated');

    $cart = Cart::where('user_id', $this->user->id)->first();
    $this->assertNotNull($cart);

    $cartItem = CartItem::where('cart_id', $cart->id)
        ->where('product_id', $this->product->id)
        ->first();

    $this->assertNotNull($cartItem);
    $this->assertEquals(1, $cartItem->quantity);
});

it('increments quantity when the same product is added twice', function () {
    Livewire::actingAs($this->user)
        ->test(AddToCart::class, ['product' => $this->product])
        ->call('addToCart')
        ->call('addToCart')
        ->assertDispatched('cartUpdated');

    $cart = Cart::where('user_id', $this->user->id)->first();
    $cartItem = CartItem::where('cart_id', $cart->id)
        ->where('product_id', $this->product->id)
        ->first();

    $this->assertNotNull($cartItem);
    $this->assertEquals(2, $cartItem->quantity);
});

it('does not allow adding more than the available stock', function () {
    Livewire::actingAs($this->user)
        ->test(AddToCart::class, ['product' => $this->product])
        ->call('addToCart')
        ->call('addToCart')
        ->call('addToCart')
        ->call('addToCart')
        ->call('addToCart')
        ->call('addToCart')
        ->call('addToCart')
        ->call('addToCart')
        ->call('addToCart')
        ->call('addToCart')
        ->call('addToCart')
        ->assertDispatched('showToast');

    $cart = Cart::where('user_id', $this->user->id)->first();
    $cartItem = CartItem::where('cart_id', $cart->id)
        ->where('product_id', $this->product->id)
        ->first();

    $this->assertEquals(10, $cartItem->quantity);
});

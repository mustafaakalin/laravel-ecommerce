<?php

use App\Filament\Resources\ProductResource;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');

    $category = Category::create(['name' => 'Form Category', 'slug' => 'form-category']);
    $brand = Brand::create(['name' => 'Form Brand', 'slug' => 'form-brand']);

    $this->product = Product::create([
        'name' => 'Form Test Product',
        'slug' => 'form-test-product',
        'description' => 'Form test description',
        'price' => 50,
        'stock' => 5,
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'sku' => 'FORM-SKU-' . uniqid(),
        'is_active' => true,
    ]);
});

it('loads the product edit form without undefined variable errors', function () {
    Livewire::actingAs($this->admin)
        ->test(ProductResource\Pages\EditProduct::class, ['record' => $this->product->getRouteKey()])
        ->assertSuccessful()
        ->assertFormFieldExists('name')
        ->assertFormSet(['name' => 'Form Test Product']);
});

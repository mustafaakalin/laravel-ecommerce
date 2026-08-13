<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');
});

$resources = [
    'AddressResource' => \App\Filament\Resources\AddressResource::class,
    'BrandResource' => \App\Filament\Resources\BrandResource::class,
    'CampaignProductResource' => \App\Filament\Resources\CampaignProductResource::class,
    'CampaignResource' => \App\Filament\Resources\CampaignResource::class,
    'CartItemResource' => \App\Filament\Resources\CartItemResource::class,
    'CartResource' => \App\Filament\Resources\CartResource::class,
    'CategoryResource' => \App\Filament\Resources\CategoryResource::class,
    'CommentResource' => \App\Filament\Resources\CommentResource::class,
    'ContactResource' => \App\Filament\Resources\ContactResource::class,
    'CouponResource' => \App\Filament\Resources\CouponResource::class,
    'FaqResource' => \App\Filament\Resources\FaqResource::class,
    'LikeResource' => \App\Filament\Resources\LikeResource::class,
    'OrderItemResource' => \App\Filament\Resources\OrderItemResource::class,
    'OrderResource' => \App\Filament\Resources\OrderResource::class,
    'PageResource' => \App\Filament\Resources\PageResource::class,
    'PermissionResource' => \App\Filament\Resources\PermissionResource::class,
    'ProductAttributeResource' => \App\Filament\Resources\ProductAttributeResource::class,
    'ProductAttributeValueResource' => \App\Filament\Resources\ProductAttributeValueResource::class,
    'ProductImageResource' => \App\Filament\Resources\ProductImageResource::class,
    'ProductResource' => \App\Filament\Resources\ProductResource::class,
    'RoleResource' => \App\Filament\Resources\RoleResource::class,
    'ShipmentDiscountResource' => \App\Filament\Resources\ShipmentDiscountResource::class,
    'ShipmentResource' => \App\Filament\Resources\ShipmentResource::class,
    'SiteSettingResource' => \App\Filament\Resources\SiteSettingResource::class,
    'SliderForHomepageResource' => \App\Filament\Resources\SliderForHomepageResource::class,
    'SoldoutResource' => \App\Filament\Resources\SoldoutResource::class,
    'TestimonialResource' => \App\Filament\Resources\TestimonialResource::class,
    'UserResource' => \App\Filament\Resources\UserResource::class,
];

foreach ($resources as $name => $resourceClass) {
    if (! isset($resourceClass::getPages()['create'])) {
        continue;
    }

    it("mounts the {$name} create form without errors", function () use ($resourceClass) {
        $pageClass = $resourceClass::getPages()['create']->getPage();

        Livewire::actingAs($this->admin)
            ->test($pageClass)
            ->assertSuccessful();
    });
}

<?php

use App\Models\Page;
use App\Models\ShipmentDiscount;
use App\Models\SiteSetting;
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

it('mounts the PageResource edit form without errors', function () {
    $page = Page::create([
        'name' => 'Test Page',
        'content' => 'Test content',
    ]);

    Livewire::actingAs($this->admin)
        ->test(\App\Filament\Resources\PageResource\Pages\EditPage::class, ['record' => $page->getRouteKey()])
        ->assertSuccessful();
});

it('mounts the ShipmentDiscountResource edit form without errors', function () {
    $discount = ShipmentDiscount::create([
        'price' => 100,
        'is_active' => true,
    ]);

    Livewire::actingAs($this->admin)
        ->test(\App\Filament\Resources\ShipmentDiscountResource\Pages\EditShipmentDiscount::class, ['record' => $discount->getRouteKey()])
        ->assertSuccessful();
});

it('mounts the SiteSettingResource edit form without errors', function () {
    $siteSetting = SiteSetting::create([
        'site_name' => 'Test Shop',
        'site_description' => 'Test description',
        'site_slogan' => 'Test slogan',
        'site_shipment_price' => 30,
    ]);

    Livewire::actingAs($this->admin)
        ->test(\App\Filament\Resources\SiteSettingResource\Pages\EditSiteSetting::class, ['record' => $siteSetting->getRouteKey()])
        ->assertSuccessful();
});

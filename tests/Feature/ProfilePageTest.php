<?php

use App\Models\Address;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function profile_test_permissions(): array
{
    return [
        'profile:view',
        'profile:update',
        'order:view',
        'order:list',
        'address:create',
        'address:view',
        'address:update',
        'address:delete',
    ];
}

beforeEach(function () {
    $role = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

    foreach (profile_test_permissions() as $permission) {
        Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
    }

    $role->syncPermissions(profile_test_permissions());

    $this->user = User::factory()->create([
        'name' => 'Test',
        'surname' => 'User',
        'phone' => '5551112233',
    ]);
    $this->user->assignRole('user');

    $this->category = Category::create(['name' => 'Test Category', 'slug' => 'test-category']);
    $this->brand = Brand::create(['name' => 'Test Brand', 'slug' => 'test-brand']);

    \App\Models\SiteSetting::create([
        'site_name' => 'Test Shop',
        'site_description' => 'Test description',
        'site_slogan' => 'Test slogan',
        'site_shipment_price' => 0,
    ]);
});

function profile_test_make_product(string $name = 'Test Product'): Product
{
    return Product::create([
        'name' => $name,
        'slug' => Str::slug($name).'-'.uniqid(),
        'description' => 'Test description',
        'price' => 100,
        'stock' => 10,
        'category_id' => test()->category->id,
        'brand_id' => test()->brand->id,
        'sku' => 'SKU-'.uniqid(),
        'is_active' => true,
    ]);
}

function profile_test_make_order(User $user, string $status = 'paid'): Order
{
    $address = Address::create([
        'user_id' => $user->id,
        'first_name' => 'Test',
        'last_name' => 'User',
        'phone' => '5551112233',
        'address' => 'Test Street No:1',
        'city' => 'İstanbul',
        'state' => 'Kadıköy',
        'country' => 'Turkey',
        'zip_code' => '34000',
    ]);

    return Order::create([
        'user_id' => $user->id,
        'address_id' => $address->id,
        'total_price' => 200,
        'status' => $status,
        'payment_method' => 'iyzico',
    ]);
}

it('renders the profile page for a user role user', function () {
    $this->actingAs($this->user)
        ->get(route('profile.show'))
        ->assertOk()
        ->assertSee('Profilim')
        ->assertSee('Siparişlerim')
        ->assertSee('Adreslerim');
});

it('renders the profile edit page for a user role user', function () {
    $this->actingAs($this->user)
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertSee('Profil Bilgileri')
        ->assertSee('Şifre Değiştir');
});

it('updates the user profile', function () {
    $this->actingAs($this->user)
        ->put(route('profile.update'), [
            'name' => 'Updated',
            'surname' => 'Name',
            'phone' => '5559998877',
            'email' => 'updated@example.com',
        ])
        ->assertRedirect(route('profile.show'));

    $this->user->refresh();

    expect($this->user->name)->toBe('Updated')
        ->and($this->user->surname)->toBe('Name')
        ->and($this->user->phone)->toBe('5559998877')
        ->and($this->user->email)->toBe('updated@example.com');
});

it('updates the user password', function () {
    $this->actingAs($this->user)
        ->put(route('profile.password'), [
            'current_password' => 'password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])
        ->assertRedirect(route('profile.show'));

    $this->user->refresh();

    expect(Hash::check('new-password-123', $this->user->password))->toBeTrue();
});

it('rejects password update with wrong current password', function () {
    $this->actingAs($this->user)
        ->put(route('profile.password'), [
            'current_password' => 'wrong-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])
        ->assertSessionHasErrors('current_password');
});

it('lists the orders of the authenticated user', function () {
    profile_test_make_order($this->user);

    $this->actingAs($this->user)
        ->get(route('profile.orders'))
        ->assertOk()
        ->assertSee('Siparişlerim')
        ->assertSee('#1');
});

it('shows the order detail of the authenticated user', function () {
    $order = profile_test_make_order($this->user);

    $product = profile_test_make_product();
    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'price' => 100,
    ]);

    $this->actingAs($this->user)
        ->get(route('profile.orders.show', $order))
        ->assertOk()
        ->assertSee('Teslimat Adresi')
        ->assertSee($product->name);
});

it('forbids viewing another users order', function () {
    $otherUser = User::factory()->create();
    $otherUser->assignRole('user');

    $order = profile_test_make_order($otherUser);

    $this->actingAs($this->user)
        ->get(route('profile.orders.show', $order))
        ->assertForbidden();
});

it('lists the addresses of the authenticated user', function () {
    $this->user->addresses()->create([
        'first_name' => 'Test',
        'last_name' => 'User',
        'phone' => '5551112233',
        'address' => 'Test Street No:1',
        'city' => 'İstanbul',
        'state' => 'Kadıköy',
        'country' => 'Turkey',
        'zip_code' => '34000',
        'is_default' => true,
    ]);

    $this->actingAs($this->user)
        ->get(route('profile.addresses.index'))
        ->assertOk()
        ->assertSee('Adreslerim')
        ->assertSee('Test Street No:1');
});

it('creates a new address for the user', function () {
    $this->actingAs($this->user)
        ->post(route('profile.addresses.store'), [
            'title' => 'home',
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone' => '5551112233',
            'address' => 'New Street No:5',
            'city' => 'Ankara',
            'state' => 'Çankaya',
            'country' => 'Turkey',
            'zip_code' => '06000',
            'is_default' => '1',
        ])
        ->assertRedirect(route('profile.addresses.index'));

    $address = $this->user->addresses()->first();

    expect($address->city)->toBe('Ankara')
        ->and($address->is_default)->toBeTrue();
});

it('updates an existing address of the user', function () {
    $address = $this->user->addresses()->create([
        'first_name' => 'Test',
        'last_name' => 'User',
        'phone' => '5551112233',
        'address' => 'Old Street',
        'city' => 'İstanbul',
        'state' => 'Kadıköy',
        'country' => 'Turkey',
        'zip_code' => '34000',
    ]);

    $this->actingAs($this->user)
        ->put(route('profile.addresses.update', $address), [
            'first_name' => 'Updated',
            'last_name' => 'User',
            'phone' => '5551112233',
            'address' => 'Updated Street',
            'city' => 'İzmir',
            'state' => 'Konak',
            'country' => 'Turkey',
            'zip_code' => '35000',
        ])
        ->assertRedirect(route('profile.addresses.index'));

    expect($address->refresh()->city)->toBe('İzmir');
});

it('forbids updating another users address', function () {
    $otherUser = User::factory()->create();
    $otherUser->assignRole('user');

    $address = $otherUser->addresses()->create([
        'first_name' => 'Other',
        'last_name' => 'User',
        'phone' => '5551112233',
        'address' => 'Other Street',
        'city' => 'İstanbul',
        'state' => 'Kadıköy',
        'country' => 'Turkey',
        'zip_code' => '34000',
    ]);

    $this->actingAs($this->user)
        ->put(route('profile.addresses.update', $address), [
            'first_name' => 'Hack',
            'last_name' => 'Attempt',
            'phone' => '5551112233',
            'address' => 'Hack Street',
            'city' => 'İstanbul',
            'state' => 'Kadıköy',
            'country' => 'Turkey',
            'zip_code' => '34000',
        ])
        ->assertForbidden();
});

it('deletes an address of the user', function () {
    $address = $this->user->addresses()->create([
        'first_name' => 'Test',
        'last_name' => 'User',
        'phone' => '5551112233',
        'address' => 'Delete Street',
        'city' => 'İstanbul',
        'state' => 'Kadıköy',
        'country' => 'Turkey',
        'zip_code' => '34000',
    ]);

    $this->actingAs($this->user)
        ->delete(route('profile.addresses.destroy', $address))
        ->assertRedirect(route('profile.addresses.index'));

    expect($this->user->addresses()->find($address->id))->toBeNull();
});

it('sets an address as default and unsets the others', function () {
    $address1 = $this->user->addresses()->create([
        'first_name' => 'Test',
        'last_name' => 'User',
        'phone' => '5551112233',
        'address' => 'First Street',
        'city' => 'İstanbul',
        'state' => 'Kadıköy',
        'country' => 'Turkey',
        'zip_code' => '34000',
        'is_default' => true,
    ]);

    $address2 = $this->user->addresses()->create([
        'first_name' => 'Test',
        'last_name' => 'User',
        'phone' => '5551112233',
        'address' => 'Second Street',
        'city' => 'İstanbul',
        'state' => 'Kadıköy',
        'country' => 'Turkey',
        'zip_code' => '34000',
    ]);

    $this->actingAs($this->user)
        ->put(route('profile.addresses.default', $address2))
        ->assertRedirect(route('profile.addresses.index'));

    expect($address1->refresh()->is_default)->toBeFalse()
        ->and($address2->refresh()->is_default)->toBeTrue();
});

it('limits the number of addresses per user to four', function () {
    foreach (range(1, 4) as $i) {
        $this->user->addresses()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone' => '5551112233',
            'address' => "Street {$i}",
            'city' => 'İstanbul',
            'state' => 'Kadıköy',
            'country' => 'Turkey',
            'zip_code' => '34000',
        ]);
    }

    $this->actingAs($this->user)
        ->post(route('profile.addresses.store'), [
            'first_name' => 'Extra',
            'last_name' => 'User',
            'phone' => '5551112233',
            'address' => 'Extra Street',
            'city' => 'İstanbul',
            'state' => 'Kadıköy',
            'country' => 'Turkey',
            'zip_code' => '34000',
        ])
        ->assertSessionHas('error', 'En fazla 4 adres ekleyebilirsiniz.');

    expect($this->user->addresses()->count())->toBe(4);
});

<?php

use App\Models\User;
use Database\Seeders\AddPermissionToRolesSeeder;
use Database\Seeders\DefaultDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns a successful response', function () {
    $this->seed(DefaultDataSeeder::class);

    $response = $this->get('/');

    $response->assertStatus(200);
});

it('only allows users with the admin role to access the panel', function () {
    $this->seed([
        AddPermissionToRolesSeeder::class,
        DefaultDataSeeder::class,
    ]);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->get('/admin')
        ->assertSuccessful();

    $customer = User::factory()->create();
    $customer->assignRole('user');

    $this->actingAs($customer)
        ->get('/admin')
        ->assertForbidden();
});

it('allows a customer to log in via the storefront login page', function () {
    $this->seed(AddPermissionToRolesSeeder::class);

    $user = User::factory()->create(['email' => 'test@example.com']);
    $user->assignRole('user');

    $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'password',
    ])->assertRedirect('/');

    $this->assertAuthenticatedAs($user);
});

it('logs a customer out via the storefront logout route', function () {
    $this->seed(AddPermissionToRolesSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('user');

    $this->actingAs($user)
        ->post('/logout')
        ->assertRedirect('/');

    $this->assertGuest();
});

it('registers a new customer with the user role', function () {
    $this->seed(AddPermissionToRolesSeeder::class);

    $this->post('/register', [
        'name' => 'Yeni Müşteri',
        'email' => 'yeni@example.com',
        'password' => 'secret-password-123',
        'password_confirmation' => 'secret-password-123',
    ])->assertRedirect('/');

    $user = User::where('email', 'yeni@example.com')->firstOrFail();

    expect($user->hasRole('user'))->toBeTrue();
    $this->assertAuthenticatedAs($user);
});

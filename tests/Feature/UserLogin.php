<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->roleId = DB::table('roles')->insertGetId([
        'name' => 'Test User',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
});

it('renders the login page', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertViewIs('auth.login');
});

it('authenticates an active user with valid credentials', function () {
    $user = User::factory()->create([
        'username' => 'test.user',
        'role_id' => $this->roleId,
        'password' => Hash::make('secret-password'),
        'is_active' => true,
    ]);

    $response = $this->post(route('login'), [
        'username' => $user->username,
        'password' => 'secret-password',
    ]);

    $response->assertRedirect(RouteServiceProvider::HOME);
    $this->assertAuthenticatedAs($user);
    expect($user->fresh()->last_login_at)->not->toBeNull();

    $this->assertDatabaseHas('login_logs', [
        'user_id' => $user->id,
        'username' => $user->username,
        'event_type' => 'login',
        'status' => 'success',
    ]);
});

it('rejects an invalid password', function () {
    $user = User::factory()->create([
        'username' => 'test.user',
        'role_id' => $this->roleId,
        'password' => Hash::make('secret-password'),
        'is_active' => true,
    ]);

    $response = $this->from(route('login'))->post(route('login'), [
        'username' => $user->username,
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect(route('login'))
        ->assertSessionHasErrors('username');
    $this->assertGuest();
});

it('does not authenticate an inactive user', function () {
    $user = User::factory()->create([
        'username' => 'inactive.user',
        'role_id' => $this->roleId,
        'password' => Hash::make('secret-password'),
        'is_active' => false,
    ]);

    $this->post(route('login'), [
        'username' => $user->username,
        'password' => 'secret-password',
    ])->assertSessionHasErrors('username');

    $this->assertGuest();
});

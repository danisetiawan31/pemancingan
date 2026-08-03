<?php

use App\Models\User;

it('can register successfully and creates a pending member', function () {
    $data = [
        'name' => 'John Doe',
        'phone' => '081234567890',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'address' => 'Jl. Kebon Jeruk No. 1',
    ];

    $response = $this->postJson('/api/register', $data);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Registrasi berhasil. Akun menunggu validasi owner.',
            'data' => [
                'name' => 'John Doe',
                'phone' => '081234567890',
                'email' => 'john@example.com',
                'address' => 'Jl. Kebon Jeruk No. 1',
                'role' => 'member',
                'status' => 'pending',
            ]
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
        'role' => 'member',
        'status' => 'pending',
    ]);
});

it('fails validation when required fields are missing', function () {
    $response = $this->postJson('/api/register', []);

    $response->assertStatus(422)
        ->assertJsonStructure([
            'success',
            'message',
            'errors' => [
                'name',
                'phone',
                'email',
                'password',
                'address',
            ]
        ]);
});

it('fails validation when phone already exists', function () {
    $user = User::factory()->create();

    $data = [
        'name' => 'Jane Doe',
        'phone' => $user->phone, // existing phone
        'email' => 'jane@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'address' => 'Jl. Melati',
    ];

    $response = $this->postJson('/api/register', $data);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['phone']);
});

it('fails validation when email already exists', function () {
    $user = User::factory()->create();

    $data = [
        'name' => 'Jane Doe',
        'phone' => '089876543210',
        'email' => $user->email, // existing email
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'address' => 'Jl. Melati',
    ];

    $response = $this->postJson('/api/register', $data);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('fails validation when password is not confirmed or shorter than 8 characters', function () {
    $data = [
        'name' => 'Jane Doe',
        'phone' => '089876543210',
        'email' => 'jane@example.com',
        'password' => 'short', // short password, no confirmation
        'address' => 'Jl. Melati',
    ];

    $response = $this->postJson('/api/register', $data);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

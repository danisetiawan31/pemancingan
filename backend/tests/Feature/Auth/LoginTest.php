<?php

use App\Models\User;

it('can login successfully using phone identifier with active status', function () {
    $user = User::factory()->create([
        'phone' => '081234567890',
        'password' => bcrypt('password123'),
        'status' => 'active',
    ]);

    $response = $this->postJson('/api/login', [
        'login' => '081234567890',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Login berhasil',
        ])
        ->assertJsonStructure([
            'data' => [
                'user' => [
                    'id', 'name', 'phone', 'email', 'address', 'role', 'status', 'created_at'
                ],
                'token'
            ]
        ]);
});

it('can login successfully using email identifier with active status', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
        'status' => 'active',
    ]);

    $response = $this->postJson('/api/login', [
        'login' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Login berhasil',
        ])
        ->assertJsonStructure([
            'data' => [
                'user',
                'token'
            ]
        ]);
});

it('fails to login on wrong password', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
        'status' => 'active',
    ]);

    $response = $this->postJson('/api/login', [
        'login' => 'test@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'message' => 'Email/Phone atau password salah',
        ]);
});

it('fails to login on non-existent login identifier', function () {
    $response = $this->postJson('/api/login', [
        'login' => 'notfound@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'message' => 'Email/Phone atau password salah',
        ]);
});

it('fails validation when login or password field is missing', function () {
    $response = $this->postJson('/api/login', []);

    $response->assertStatus(422)
        ->assertJsonStructure([
            'success',
            'message',
            'errors' => [
                'login',
                'password',
            ]
        ]);
});

it('blocks login when status is pending', function () {
    $user = User::factory()->pendingMember()->create([
        'email' => 'pending@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'login' => 'pending@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(403)
        ->assertJson([
            'success' => false,
            'message' => 'Akun masih menunggu validasi owner'
        ]);
});

it('blocks login when status is rejected', function () {
    $user = User::factory()->rejectedMember()->create([
        'email' => 'rejected@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'login' => 'rejected@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(403)
        ->assertJson([
            'success' => false,
        ])
        ->assertJsonFragment([
            'rejection_reason' => $user->rejection_reason,
        ]);
        
    expect($response->json('message'))->toContain('Akun ditolak oleh owner');
});

it('blocks login when status is deactivated', function () {
    $user = User::factory()->deactivatedMember()->create([
        'email' => 'deactivated@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'login' => 'deactivated@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(403)
        ->assertJson([
            'success' => false,
        ])
        ->assertJsonFragment([
            'deactivated_reason' => $user->deactivated_reason,
        ]);
        
    expect($response->json('message'))->toContain('Akun Anda telah dinonaktifkan');
});

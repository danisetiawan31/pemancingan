<?php

use App\Models\User;

it('returns authenticated user data with 200', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->getJson('/api/me');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
                'address' => $user->address,
                'role' => $user->role,
                'status' => $user->status,
            ]
        ]);
});

it('returns 401 when unauthenticated user requests me endpoint', function () {
    $response = $this->getJson('/api/me');

    $response->assertStatus(401);
});

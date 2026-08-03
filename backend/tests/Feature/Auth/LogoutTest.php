<?php

use App\Models\User;

it('can log out successfully and invalidate the token', function () {
    $user = User::factory()->create();
    $newToken = $user->createToken('auth-token');

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $newToken->plainTextToken,
    ])->postJson('/api/logout');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Logout berhasil',
        ]);

    $this->assertDatabaseMissing('personal_access_tokens', [
        'id' => $newToken->accessToken->id,
    ]);
});

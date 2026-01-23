<?php

use App\Models\User;
use function Pest\Laravel\postJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\actingAs;

test('a user can register', function () {
    $response = postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'access_token',
            'token_type',
            'user',
        ]);
});

test('a user can login', function () {
    $user = User::factory()->create([
        'email' => 'login@example.com',
        'password' => bcrypt('password'),
    ]);

    $response = postJson('/api/login', [
        'email' => 'login@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'access_token',
            'token_type',
            'user',
        ]);
});

test('authenticated user can get their profile', function () {
    $user = User::factory()->create();
    
    // Create token manually or use actingAsSanctum equivalent if available, 
    // but actingAs for Sanctum often requires setup. 
    // Simplest is to just login and use the token, or use Laravel's actingAs with Sanctum guard.
    
    $response = actingAs($user, 'sanctum')->getJson('/api/user');

    $response->assertStatus(200)
        ->assertJson([
            'id' => $user->id,
            'email' => $user->email,
        ]);
});

test('authenticated user can logout', function () {
    $user = User::factory()->create();
    
    // Check if actingAs works for token deletion which relies on currentAccessToken()
    // currentAccessToken() might be null if we just use actingAs($user).
    // We need to actually create a token and authenticate with it to test currentAccessToken()->delete().
    
    $token = $user->createToken('test-token')->plainTextToken;
    
    $response = getJson('/api/user', [
        'Authorization' => 'Bearer ' . $token,
    ]);
    $response->assertStatus(200); // Verify token works

    $response = postJson('/api/logout', [], [
        'Authorization' => 'Bearer ' . $token,
    ]);

    $response->assertStatus(200)
        ->assertJson(['message' => 'Successfully logged out']);
        
    // Verify token is gone (optional, but good for completeness)
    $this->assertDatabaseCount('personal_access_tokens', 0);
});

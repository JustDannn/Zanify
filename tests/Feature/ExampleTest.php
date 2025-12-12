<?php

test('returns a successful response', function () {
    // 1. Buat user palsu (menggunakan factory)
    $user = User::factory()->create();

    // 2. Gunakan actingAs() untuk berpura-pura user sudah login
    $response = $this->actingAs($user)->get('/');
    $response->assertStatus(200);
});
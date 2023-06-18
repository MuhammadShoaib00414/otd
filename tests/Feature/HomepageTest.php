<?php

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can see homepage', function () {
    $this->get('/')
         ->assertStatus(200);
});

test('redirects when logged in', function () {
    $user = factory(User::class)->create();
    
    $this->actingAs($user)
         ->get('/login')
         ->assertRedirect('/home');
});
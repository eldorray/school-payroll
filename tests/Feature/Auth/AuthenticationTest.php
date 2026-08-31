<?php

namespace Tests\Feature\Auth;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();
        $unit = Unit::create(['name' => 'MI Test', 'code' => 'MI']);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'unit_id' => $unit->id,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        // Unit terpilih dipakai semua modul lewat session
        $this->assertEquals($unit->id, session('unit_id'));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $unit = Unit::create(['name' => 'MI Test', 'code' => 'MI']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
            'unit_id' => $unit->id,
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}

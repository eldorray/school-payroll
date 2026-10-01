<?php

namespace Tests\Feature;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnitSwitchTest extends TestCase
{
    use RefreshDatabase;

    public function test_switching_unit_preserves_login_and_selected_period(): void
    {
        $first = Unit::create(['name' => 'Unit Pertama', 'code' => 'A']);
        $second = Unit::create(['name' => 'Unit Kedua', 'code' => 'B']);
        $user = User::factory()->create();
        $this->actingAs($user)->withSession(['unit_id' => $first->id]);

        $this->get(route('dashboard'))->assertOk()->assertSee('summary-unit')->assertSee('Unit Kedua');
        $this->post(route('units.switch'), ['unit_id' => $second->id, 'month' => 9, 'year' => 2026])
            ->assertRedirect(route('dashboard', ['month' => 9, 'year' => 2026]))
            ->assertSessionHas('unit_id', $second->id);
        $this->assertAuthenticatedAs($user);
        $this->get(route('dashboard', ['month' => 9, 'year' => 2026]))->assertOk()
            ->assertViewHas('unit', fn ($unit) => $unit->id === $second->id);

        foreach ([[], ['unit_id' => 999999], ['unit_id' => 'invalid'], ['unit_id' => $first->id, 'month' => 13]] as $input) {
            $this->post(route('units.switch'), $input)->assertSessionHasErrors()
                ->assertSessionHas('unit_id', $second->id);
        }
    }

    public function test_guests_cannot_switch_unit(): void
    {
        $unit = Unit::create(['name' => 'Unit Sekolah', 'code' => 'A']);
        $this->post(route('units.switch'), ['unit_id' => $unit->id])->assertRedirect(route('login'));
    }
}

<?php

namespace Tests\Feature;

use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Selamat Datang');
    }

    public function test_dashboard_shows_user_name(): void
    {
        $user = User::factory()->create(['name' => 'John Doe']);

        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('John Doe');
    }

    public function test_dashboard_shows_class_stats(): void
    {
        $user = User::factory()->create();
        ClassModel::factory()->count(3)->forUser($user)->create();

        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('3');
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_dashboard_shows_quick_actions(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Buat Absensi');
        $response->assertSee('Tambah Siswa');
    }
}

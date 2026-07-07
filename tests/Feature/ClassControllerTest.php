<?php

namespace Tests\Feature;

use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_view_class_list(): void
    {
        ClassModel::factory()->count(3)->forUser($this->user)->create();

        $response = $this->actingAs($this->user)
            ->get(route('classes.index'));

        $response->assertStatus(200);
        $response->assertSee('Kelas');
    }

    public function test_user_can_create_class(): void
    {
        $response = $this->actingAs($this->user)->post(route('classes.store'), [
            'name' => 'X IPA 1',
            'jurusan' => 'IPA',
            'tingkat' => 'X',
            'school_year_start' => 2024,
            'school_year_end' => 2025,
        ]);

        $response->assertRedirect(route('classes.show', ClassModel::first()));

        $this->assertDatabaseHas('classes', [
            'name' => 'X IPA 1',
            'jurusan' => 'IPA',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_user_can_view_class_detail(): void
    {
        $class = ClassModel::factory()->forUser($this->user)->create([
            'name' => 'X IPA 1',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('classes.show', $class));

        $response->assertStatus(200);
        $response->assertSee('X IPA 1');
    }

    public function test_user_can_update_class(): void
    {
        $class = ClassModel::factory()->forUser($this->user)->create();

        $response = $this->actingAs($this->user)->put(route('classes.update', $class), [
            'name' => 'Updated Class',
            'jurusan' => 'IPS',
            'tingkat' => 'XI',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('classes', [
            'id' => $class->id,
            'name' => 'Updated Class',
        ]);
    }

    public function test_user_can_delete_class(): void
    {
        $class = ClassModel::factory()->forUser($this->user)->create();

        $response = $this->actingAs($this->user)
            ->delete(route('classes.destroy', $class));

        $response->assertRedirect(route('classes.index'));

        $this->assertSoftDeleted('classes', ['id' => $class->id]);
    }

    public function test_user_cannot_view_other_users_class(): void
    {
        $otherUser = User::factory()->create();
        $class = ClassModel::factory()->forUser($otherUser)->create();

        $response = $this->actingAs($this->user)
            ->get(route('classes.show', $class));

        $response->assertStatus(403);
    }

    public function test_validation_required_fields_on_create(): void
    {
        $response = $this->actingAs($this->user)->post(route('classes.store'), []);

        $response->assertSessionHasErrors(['name', 'jurusan', 'tingkat']);
    }
}

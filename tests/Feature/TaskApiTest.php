<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['is_admin' => true]);
    }

    public function test_update_tbcb_date(): void
    {
        $task = Task::factory()->unplanned()->create();

        $response = $this->actingAs($this->user)
            ->patchJson("/admin/api/tasks/{$task->id}", [
                'tbcb_date' => '2026-06-01',
            ]);

        $response->assertOk();
        $this->assertEquals('2026-06-01', $task->fresh()->tbcb_date->toDateString());
    }

    public function test_clear_tbcb_date(): void
    {
        $task = Task::factory()->planned('2026-05-30')->create();

        $response = $this->actingAs($this->user)
            ->patchJson("/admin/api/tasks/{$task->id}", [
                'tbcb_date' => null,
            ]);

        $response->assertOk();
        $this->assertNull($task->fresh()->tbcb_date);
    }

    public function test_update_status(): void
    {
        $task = Task::factory()->create(['status' => 'backlog']);

        $response = $this->actingAs($this->user)
            ->patchJson("/admin/api/tasks/{$task->id}/status", [
                'status' => 'wip',
            ]);

        $response->assertOk();
        $this->assertEquals('wip', $task->fresh()->status);
    }

    public function test_update_impact_rating(): void
    {
        $task = Task::factory()->create(['impact_rating' => 2]);

        $response = $this->actingAs($this->user)
            ->patchJson("/admin/api/tasks/{$task->id}/impact", [
                'impact_rating' => 4,
            ]);

        $response->assertOk();
        $this->assertEquals(4, $task->fresh()->impact_rating);
    }

    public function test_defer_task(): void
    {
        $task = Task::factory()->create(['status' => 'backlog', 'title' => 'Defer me']);

        $response = $this->actingAs($this->user)
            ->postJson("/admin/api/tasks/{$task->id}/defer");

        $response->assertOk();
        // Defer deletes the original and creates a rolled-over copy for tomorrow
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Defer me',
            'is_rolled_over' => true,
        ]);
    }

    public function test_delete_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->actingAs($this->user)
            ->deleteJson("/admin/api/tasks/{$task->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_update_title(): void
    {
        $task = Task::factory()->create(['title' => 'Old title']);

        $response = $this->actingAs($this->user)
            ->patchJson("/admin/api/tasks/{$task->id}", [
                'title' => 'New title',
            ]);

        $response->assertOk();
        $this->assertEquals('New title', $task->fresh()->title);
    }

    public function test_update_rejects_invalid_status(): void
    {
        $task = Task::factory()->create();

        $response = $this->actingAs($this->user)
            ->patchJson("/admin/api/tasks/{$task->id}/status", [
                'status' => 'invalid',
            ]);

        $response->assertUnprocessable();
    }

    public function test_unauthenticated_cannot_access_task_api(): void
    {
        $task = Task::factory()->create();

        $response = $this->patchJson("/admin/api/tasks/{$task->id}", ['title' => 'Hacked']);

        $response->assertRedirect();
    }
}

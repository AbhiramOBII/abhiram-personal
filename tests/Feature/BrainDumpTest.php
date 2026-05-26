<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrainDumpTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['is_admin' => true]);
    }

    public function test_confirm_creates_tasks_with_tbcb_date_and_value_score(): void
    {
        $payload = [
            'tasks' => [
                [
                    'title' => 'Test task one',
                    'pillar' => 'revenue',
                    'priority' => 'must',
                    'tbcb_date' => '2026-05-30',
                    'value_score' => 75,
                    'notes' => 'Some notes',
                ],
                [
                    'title' => 'Test task two',
                    'pillar' => 'operations',
                    'priority' => 'should',
                    'tbcb_date' => '2026-06-01',
                    'value_score' => 50,
                    'notes' => '',
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/admin/api/dump/confirm', $payload);

        $response->assertOk()
            ->assertJson(['success' => true, 'created' => 2]);

        $task1 = Task::where('title', 'Test task one')->first();
        $this->assertNotNull($task1);
        $this->assertEquals('revenue', $task1->pillar);
        $this->assertEquals('must', $task1->priority);
        $this->assertEquals('2026-05-30', $task1->tbcb_date->toDateString());
        $this->assertEquals(75, $task1->value_score);
        $this->assertEquals('backlog', $task1->status);

        $task2 = Task::where('title', 'Test task two')->first();
        $this->assertNotNull($task2);
        $this->assertEquals('operations', $task2->pillar);
        $this->assertEquals('2026-06-01', $task2->tbcb_date->toDateString());
        $this->assertEquals(50, $task2->value_score);
    }

    public function test_confirm_creates_tasks_without_tbcb_date(): void
    {
        $payload = [
            'tasks' => [
                [
                    'title' => 'Floating task',
                    'pillar' => 'personal',
                    'priority' => 'bonus',
                    'tbcb_date' => null,
                    'value_score' => null,
                    'notes' => '',
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/admin/api/dump/confirm', $payload);

        $response->assertOk()
            ->assertJson(['success' => true, 'created' => 1]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Floating task',
            'tbcb_date' => null,
            'daily_plan_id' => null,
            'value_score' => 0,
        ]);
    }

    public function test_confirm_skips_empty_titles(): void
    {
        $payload = [
            'tasks' => [
                ['title' => '', 'pillar' => 'ops', 'priority' => 'should', 'tbcb_date' => null, 'value_score' => null, 'notes' => ''],
                ['title' => '   ', 'pillar' => 'ops', 'priority' => 'should', 'tbcb_date' => null, 'value_score' => null, 'notes' => ''],
                ['title' => 'Valid task', 'pillar' => 'ops', 'priority' => 'should', 'tbcb_date' => null, 'value_score' => 40, 'notes' => ''],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/admin/api/dump/confirm', $payload);

        $response->assertOk()
            ->assertJson(['success' => true, 'created' => 1]);
    }

    public function test_confirm_skips_deselected_tasks(): void
    {
        $payload = [
            'tasks' => [
                ['title' => 'Selected task', 'pillar' => 'revenue', 'priority' => 'must', 'tbcb_date' => '2026-05-30', 'value_score' => 60, 'notes' => ''],
                ['title' => 'Deselected task', 'pillar' => 'ops', 'priority' => 'should', 'tbcb_date' => null, 'value_score' => 30, 'notes' => '', 'selected' => false],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/admin/api/dump/confirm', $payload);

        $response->assertOk()
            ->assertJson(['success' => true, 'created' => 1]);

        $this->assertDatabaseMissing('tasks', ['title' => 'Deselected task']);
    }

    public function test_confirm_requires_tasks_array(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/admin/api/dump/confirm', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('tasks');
    }

    public function test_confirm_rejects_unauthenticated(): void
    {
        $response = $this->postJson('/admin/api/dump/confirm', ['tasks' => []]);

        $response->assertRedirect();
    }
}

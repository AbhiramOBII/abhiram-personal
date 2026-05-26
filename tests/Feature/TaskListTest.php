<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskListTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['is_admin' => true]);
    }

    public function test_task_list_page_loads(): void
    {
        $response = $this->actingAs($this->user)->get('/admin/tasks');

        $response->assertOk();
    }

    public function test_task_list_shows_planned_tasks_for_current_week(): void
    {
        $task = Task::factory()->planned(now()->startOfWeek()->addDay()->toDateString())
            ->create(['title' => 'This week task']);

        $response = $this->actingAs($this->user)->get('/admin/tasks?range=week');

        $response->assertOk();
        $response->assertSee('This week task');
    }

    public function test_task_list_shows_unplanned_tasks(): void
    {
        $task = Task::factory()->unplanned()->create(['title' => 'Floating task']);

        $response = $this->actingAs($this->user)->get('/admin/tasks?range=week');

        $response->assertOk();
        $response->assertSee('Floating task');
    }

    public function test_task_list_filters_by_pillar(): void
    {
        Task::factory()->unplanned()->create(['title' => 'Revenue task', 'pillar' => 'revenue']);
        Task::factory()->unplanned()->create(['title' => 'Ops task', 'pillar' => 'operations']);

        $response = $this->actingAs($this->user)->get('/admin/tasks?pillar=revenue');

        $response->assertOk();
        $response->assertSee('Revenue task');
        $response->assertDontSee('Ops task');
    }

    public function test_task_list_filters_by_status(): void
    {
        Task::factory()->unplanned()->create(['title' => 'Backlog task', 'status' => 'backlog']);
        Task::factory()->unplanned()->wip()->create(['title' => 'WIP task']);

        $response = $this->actingAs($this->user)->get('/admin/tasks?status=wip');

        $response->assertOk();
        $response->assertSee('WIP task');
        $response->assertDontSee('Backlog task');
    }

    public function test_task_list_excludes_tasks_outside_date_range(): void
    {
        Task::factory()->planned(now()->subMonth()->toDateString())
            ->create(['title' => 'Old task']);

        $response = $this->actingAs($this->user)->get('/admin/tasks?range=week');

        $response->assertOk();
        $response->assertDontSee('Old task');
    }

    public function test_task_list_month_range_includes_current_month_tasks(): void
    {
        Task::factory()->planned(now()->startOfMonth()->addDays(5)->toDateString())
            ->create(['title' => 'Month task']);

        $response = $this->actingAs($this->user)->get('/admin/tasks?range=month');

        $response->assertOk();
        $response->assertSee('Month task');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskScopesTest extends TestCase
{
    use RefreshDatabase;

    public function test_unplanned_scope_returns_tasks_without_tbcb_date(): void
    {
        Task::factory()->unplanned()->create(['title' => 'No date']);
        Task::factory()->planned('2026-05-30')->create(['title' => 'Has date']);
        Task::factory()->unplanned()->done()->create(['title' => 'Done no date']);

        $unplanned = Task::unplanned()->get();

        $this->assertCount(1, $unplanned);
        $this->assertEquals('No date', $unplanned->first()->title);
    }

    public function test_planned_scope_returns_tasks_with_tbcb_date(): void
    {
        Task::factory()->planned('2026-05-30')->create(['title' => 'Planned']);
        Task::factory()->unplanned()->create(['title' => 'Unplanned']);

        $planned = Task::planned()->get();

        $this->assertCount(1, $planned);
        $this->assertEquals('Planned', $planned->first()->title);
    }

    public function test_floating_scope_is_alias_for_unplanned(): void
    {
        Task::factory()->unplanned()->create(['title' => 'Float']);
        Task::factory()->planned('2026-05-28')->create(['title' => 'Planned']);

        $floating = Task::floating()->get();
        $unplanned = Task::unplanned()->get();

        $this->assertEquals($floating->pluck('id'), $unplanned->pluck('id'));
    }

    public function test_tbcb_due_today_returns_overdue_and_today_tasks(): void
    {
        Task::factory()->planned(today()->toDateString())->create(['title' => 'Due today']);
        Task::factory()->overdue()->create(['title' => 'Overdue']);
        Task::factory()->planned(today()->addDays(3)->toDateString())->create(['title' => 'Future']);
        Task::factory()->unplanned()->create(['title' => 'No plan']);

        $due = Task::tbcbDueToday()->get();

        $this->assertCount(2, $due);
        $this->assertTrue($due->pluck('title')->contains('Due today'));
        $this->assertTrue($due->pluck('title')->contains('Overdue'));
        $this->assertFalse($due->pluck('title')->contains('Future'));
        $this->assertFalse($due->pluck('title')->contains('No plan'));
    }

    public function test_unplanned_excludes_archived_tasks(): void
    {
        Task::factory()->unplanned()->create(['title' => 'Active']);
        Task::factory()->unplanned()->create(['title' => 'Archived', 'archived_at' => now()]);

        $unplanned = Task::unplanned()->get();

        $this->assertCount(1, $unplanned);
        $this->assertEquals('Active', $unplanned->first()->title);
    }

    public function test_unplanned_excludes_subtasks(): void
    {
        $parent = Task::factory()->unplanned()->create(['title' => 'Parent']);
        Task::factory()->unplanned()->create(['title' => 'Child', 'parent_task_id' => $parent->id]);

        $unplanned = Task::unplanned()->get();

        $this->assertCount(1, $unplanned);
        $this->assertEquals('Parent', $unplanned->first()->title);
    }

    public function test_unplanned_excludes_done_and_deferred_tasks(): void
    {
        Task::factory()->unplanned()->create(['title' => 'Backlog']);
        Task::factory()->unplanned()->wip()->create(['title' => 'WIP']);
        Task::factory()->unplanned()->done()->create(['title' => 'Done']);
        Task::factory()->unplanned()->deferred()->create(['title' => 'Deferred']);

        $unplanned = Task::unplanned()->get();

        $this->assertCount(2, $unplanned);
        $titles = $unplanned->pluck('title')->toArray();
        $this->assertContains('Backlog', $titles);
        $this->assertContains('WIP', $titles);
    }
}

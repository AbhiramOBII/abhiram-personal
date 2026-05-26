<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'pillar' => fake()->randomElement(['revenue', 'operations', 'marketing', 'creation', 'networking', 'personal']),
            'priority' => fake()->randomElement(['must', 'should', 'bonus']),
            'status' => 'backlog',
            'task_type' => 'daily',
            'is_completed' => false,
            'rollover_count' => 0,
            'impact_rating' => 2,
            'value_score' => fake()->numberBetween(20, 90),
        ];
    }

    public function planned(?string $date = null): static
    {
        return $this->state(fn() => [
            'tbcb_date' => $date ?? now()->addDays(2)->toDateString(),
        ]);
    }

    public function unplanned(): static
    {
        return $this->state(fn() => [
            'tbcb_date' => null,
        ]);
    }

    public function wip(): static
    {
        return $this->state(fn() => ['status' => 'wip']);
    }

    public function done(): static
    {
        return $this->state(fn() => ['status' => 'done', 'is_completed' => true]);
    }

    public function deferred(): static
    {
        return $this->state(fn() => ['status' => 'deferred']);
    }

    public function overdue(): static
    {
        return $this->state(fn() => [
            'tbcb_date' => now()->subDays(2)->toDateString(),
        ]);
    }
}

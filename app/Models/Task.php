<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_plan_id',
        'time_block_id',
        'title',
        'notes',
        'pillar',
        'priority',
        'estimated_minutes',
        'status',
        'task_type',
        'start_date',
        'deadline_at',
        'deadline_notes',
        'deadline_notified_3d',
        'deadline_notified_1d',
        'deadline_notified_0d',
        'is_completed',
        'completed_at',
        'is_rolled_over',
        'rolled_from_date',
        'rollover_count',
        'sort_order',
        'parent_task_id',
        'is_recurring',
        'recurring_days',
        'recurring_type',
        'due_date',
        'archived_at',
        'impact_rating',
        'value_score',
        'theme_score',
        'urgency_score',
        'efficiency_score',
        'value_score_calculated_for',
        'is_resurfaced',
        'resurfaced_on',
        'tbcb_date',
    ];

    protected function casts(): array
    {
        return [
            'is_rolled_over' => 'boolean',
            'is_recurring' => 'boolean',
            'completed_at' => 'datetime',
            'rolled_from_date' => 'date',
            'due_date' => 'date',
            'archived_at' => 'datetime',
            'recurring_days' => 'array',
            'deadline_at' => 'datetime',
            'start_date' => 'date',
            'deadline_notified_3d' => 'boolean',
            'deadline_notified_1d' => 'boolean',
            'deadline_notified_0d' => 'boolean',
            'impact_rating' => 'integer',
            'value_score' => 'integer',
            'theme_score' => 'integer',
            'urgency_score' => 'integer',
            'efficiency_score' => 'integer',
            'value_score_calculated_for' => 'date',
            'is_resurfaced' => 'boolean',
            'resurfaced_on' => 'date',
            'tbcb_date' => 'date',
        ];
    }

    // ─── Status accessor — keeps is_completed working everywhere ───
    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'done';
    }

    // ─── Status helpers ───
    public function markBacklog(): void
    {
        $this->update(['status' => 'backlog', 'is_completed' => false, 'completed_at' => null]);
    }

    public function markWip(): void
    {
        $this->update(['status' => 'wip', 'is_completed' => false]);
    }

    public function markDone(): void
    {
        $this->update(['status' => 'done', 'is_completed' => true, 'completed_at' => now()]);
    }

    public function markDeferred(): void
    {
        $this->update(['status' => 'deferred', 'is_completed' => false]);
    }

    public function cycleStatus(): void
    {
        $cycle = ['backlog' => 'wip', 'wip' => 'done', 'done' => 'backlog', 'deferred' => 'backlog'];
        $next = $cycle[$this->status] ?? 'backlog';
        match ($next) {
            'wip'     => $this->markWip(),
            'done'    => $this->markDone(),
            'backlog' => $this->markBacklog(),
            default   => $this->markBacklog(),
        };
    }

    // ─── Status config ───
    public static function statusConfig(): array
    {
        return [
            'backlog'  => ['label' => 'Backlog',  'color' => '#7a7974', 'bg' => '#7a797422', 'dot' => '⬤', 'emoji' => '📋'],
            'wip'      => ['label' => 'WIP',      'color' => '#006494', 'bg' => '#00649422', 'dot' => '⬤', 'emoji' => '⚡'],
            'done'     => ['label' => 'Done',     'color' => '#437a22', 'bg' => '#437a2222', 'dot' => '⬤', 'emoji' => '✅'],
            'deferred' => ['label' => 'Deferred', 'color' => '#964219', 'bg' => '#96421922', 'dot' => '⬤', 'emoji' => '⏭️'],
        ];
    }

    public function getStatusConfigAttribute(): array
    {
        return self::statusConfig()[$this->status] ?? self::statusConfig()['backlog'];
    }

    // ─── Relationships ───
    public function dailyPlan(): BelongsTo
    {
        return $this->belongsTo(DailyPlan::class);
    }

    public function timeBlock(): BelongsTo
    {
        return $this->belongsTo(TimeBlock::class);
    }

    public function subTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    public function deadlineAlerts(): HasMany
    {
        return $this->hasMany(DeadlineAlert::class);
    }

    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    // ─── Legacy helpers (delegate to status) ───
    public function complete(): void
    {
        $this->markDone();
    }

    public function defer(): void
    {
        $tomorrow = now()->addDay()->toDateString();
        $tomorrowDay = WorkingDay::where('day_number', now()->addDay()->dayOfWeek)->first();

        $tomorrowPlan = DailyPlan::firstOrCreate(
            ['plan_date' => $tomorrow],
            ['working_day_id' => $tomorrowDay?->id]
        );

        Task::create([
            'daily_plan_id' => $tomorrowPlan->id,
            'time_block_id' => null,
            'title' => $this->title,
            'notes' => $this->notes,
            'pillar' => $this->pillar,
            'priority' => $this->priority,
            'estimated_minutes' => $this->estimated_minutes,
            'status' => 'backlog',
            'is_completed' => false,
            'is_rolled_over' => true,
            'rolled_from_date' => now()->toDateString(),
            'rollover_count' => $this->rollover_count + 1,
            'sort_order' => 0,
        ]);

        $this->delete();
    }

    // ─── Scopes ───
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'done');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', ['backlog', 'wip']);
    }

    public function scopeBacklog(Builder $query): Builder
    {
        return $query->where('status', 'backlog');
    }

    public function scopeWip(Builder $query): Builder
    {
        return $query->where('status', 'wip');
    }

    public function scopeDone(Builder $query): Builder
    {
        return $query->where('status', 'done');
    }

    public function scopeDeferred(Builder $query): Builder
    {
        return $query->where('status', 'deferred');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('archived_at');
    }

    public function scopeNotDone(Builder $query): Builder
    {
        return $query->where('status', '!=', 'done');
    }

    public function scopeForBlock(Builder $query, $blockId): Builder
    {
        return $query->where('time_block_id', $blockId);
    }

    public function scopeRecurring(Builder $query): Builder
    {
        return $query->where('is_recurring', true);
    }

    public function scopeDaily(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('task_type', 'daily')->orWhereNull('task_type');
        });
    }

    public function scopeProject(Builder $query): Builder
    {
        return $query->where('task_type', 'project');
    }

    public function scopeActiveProjects(Builder $query): Builder
    {
        return $query->project()
            ->whereIn('status', ['backlog', 'wip'])
            ->where('start_date', '<=', today())
            ->where(function ($q) {
                $q->whereNull('deadline_at')
                  ->orWhere('deadline_at', '>=', now()->startOfDay());
            });
    }

    public function scopeOverdueProjects(Builder $query): Builder
    {
        return $query->project()
            ->whereIn('status', ['backlog', 'wip'])
            ->where('deadline_at', '<', now());
    }

    public function archive(): void
    {
        $this->archived_at = now();
        $this->save();
    }

    // ─── Scopes for DayOS Sync ───
    public function scopeUnplanned(Builder $query): Builder
    {
        return $query->active()
            ->whereNull('parent_task_id')
            ->whereIn('status', ['backlog', 'wip'])
            ->whereNull('tbcb_date');
    }

    public function scopeFloating(Builder $query): Builder
    {
        return $query->unplanned();
    }

    public function scopePlanned(Builder $query): Builder
    {
        return $query->active()
            ->whereNull('parent_task_id')
            ->whereIn('status', ['backlog', 'wip'])
            ->whereNotNull('tbcb_date');
    }

    public function scopeTbcbDueToday(Builder $query): Builder
    {
        return $query->planned()
            ->where('tbcb_date', '<=', today());
    }

    // ─── TBCB Accessors ───
    public function getTbcbBadgeAttribute(): ?array
    {
        if (!$this->tbcb_date) return null;
        $isOverdue = $this->tbcb_date->isBefore(today());
        return [
            'date' => $this->tbcb_date->toDateString(),
            'formatted' => $this->tbcb_date->format('j M'),
            'is_overdue' => $isOverdue,
        ];
    }

    public function getTbcbFormattedAttribute(): ?string
    {
        if (!$this->tbcb_date) return null;
        return $this->tbcb_date->format('j M');
    }

    // ─── Value Score Accessors ───
    public function getImpactLabelAttribute(): string
    {
        return match ((int) $this->impact_rating) {
            4 => 'Critical',
            3 => 'High',
            2 => 'Medium',
            1 => 'Low',
            0 => 'Minimal',
            default => 'Medium',
        };
    }

    public function getImpactColorAttribute(): string
    {
        return match ((int) $this->impact_rating) {
            4 => '#a13544',
            3 => '#da7101',
            2 => '#d19900',
            1 => '#437a22',
            0 => '#7a7974',
            default => '#d19900',
        };
    }

    public function getScoreBadgeAttribute(): array
    {
        $score = $this->value_score;
        return [
            'score' => $score,
            'tier'  => match (true) {
                $score >= 75 => 'critical',
                $score >= 55 => 'high',
                $score >= 35 => 'medium',
                default      => 'low',
            },
            'color' => match (true) {
                $score >= 75 => '#a13544',
                $score >= 55 => '#da7101',
                $score >= 35 => '#d19900',
                default      => '#7a7974',
            },
            'bg' => match (true) {
                $score >= 75 => '#a1354422',
                $score >= 55 => '#da710122',
                $score >= 35 => '#d1990022',
                default      => '#7a797422',
            },
        ];
    }

    public function isOverdue(): bool
    {
        return $this->due_date !== null
            && $this->due_date->isBefore(today())
            && $this->status !== 'done';
    }

    public function isProject(): bool
    {
        return $this->task_type === 'project';
    }

    public function isDaily(): bool
    {
        return $this->task_type !== 'project';
    }

    public function getDeadlineProximityAttribute(): ?string
    {
        if (!$this->deadline_at || $this->task_type !== 'project') return null;
        if ($this->status === 'done') return null;
        $diff = (int) now()->startOfDay()->diffInDays($this->deadline_at->startOfDay(), false);
        if ($diff < 0)  return 'overdue';
        if ($diff === 0) return '0d';
        if ($diff === 1) return '1d';
        if ($diff <= 3)  return '3d';
        return null;
    }

    public function getDeadlineBadgeAttribute(): ?array
    {
        $proximity = $this->deadline_proximity;
        if (!$proximity) return null;
        $daysLeft = $this->deadline_at ? (int) now()->startOfDay()->diffInDays($this->deadline_at->startOfDay(), false) : 0;
        $map = [
            'overdue' => ['label' => 'Overdue',      'color' => '#a12c7b', 'bg' => '#a12c7b22', 'icon' => '💀'],
            '0d'      => ['label' => 'Due today',    'color' => '#a13544', 'bg' => '#a1354422', 'icon' => '🔴'],
            '1d'      => ['label' => 'Due tomorrow', 'color' => '#da7101', 'bg' => '#da710122', 'icon' => '🟠'],
            '3d'      => ['label' => 'Due in ' . $daysLeft . ' days', 'color' => '#d19900', 'bg' => '#d1990022', 'icon' => '🟡'],
        ];
        return $map[$proximity] ?? null;
    }

    public function getDeadlineFormattedAttribute(): ?string
    {
        if (!$this->deadline_at) return null;
        $diff = (int) now()->startOfDay()->diffInDays($this->deadline_at->startOfDay(), false);
        if ($diff < 0)  return 'Overdue since ' . $this->deadline_at->format('d M, g:i A');
        if ($diff === 0) return 'Due today at ' . $this->deadline_at->format('g:i A');
        if ($diff === 1) return 'Due tomorrow at ' . $this->deadline_at->format('g:i A');
        return 'Due ' . $this->deadline_at->format('d M') . ' at ' . $this->deadline_at->format('g:i A');
    }
}

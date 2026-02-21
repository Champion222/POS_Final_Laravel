<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivityLogFilterRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(ActivityLogFilterRequest $request): View
    {
        $validated = $request->validated();
        $range = $this->resolveDateRange($validated['range'] ?? null);
        $filters = [
            'range' => $range['key'],
            'user_id' => isset($validated['user_id']) ? (int) $validated['user_id'] : null,
            'event' => isset($validated['event']) ? (string) $validated['event'] : null,
            'per_page' => isset($validated['per_page']) ? (int) $validated['per_page'] : 10,
            'search' => trim((string) ($validated['search'] ?? '')),
        ];

        $activitiesQuery = ActivityLog::query()
            ->with('user')
            ->latest();

        $this->applyFilters($activitiesQuery, $filters, $range);

        $stats = $this->buildStats(clone $activitiesQuery);

        $activities = $activitiesQuery
            ->paginate($filters['per_page'])
            ->withQueryString();

        $actors = User::query()
            ->whereIn('id', ActivityLog::query()->select('user_id')->whereNotNull('user_id')->distinct())
            ->orderBy('name')
            ->get(['id', 'name', 'role']);

        $events = ActivityLog::query()
            ->select('event')
            ->distinct()
            ->orderBy('event')
            ->pluck('event');

        return view('activities.index', [
            'activities' => $activities,
            'actors' => $actors,
            'events' => $events,
            'stats' => $stats,
            'filters' => $filters,
            'rangeLabel' => $range['label'],
            'rangeDescription' => $range['description'],
        ]);
    }

    /**
     * @param  array{range:string, user_id:int|null, event:string|null, per_page:int, search:string}  $filters
     * @param  array{key:string, label:string, description:string, start:Carbon|null, end:Carbon|null}  $range
     */
    private function applyFilters(Builder $query, array $filters, array $range): void
    {
        if ($range['start'] !== null && $range['end'] !== null) {
            $query->whereBetween('created_at', [$range['start'], $range['end']]);
        }

        if ($filters['user_id'] !== null) {
            $query->where('user_id', $filters['user_id']);
        }

        if ($filters['event'] !== null && $filters['event'] !== '') {
            $query->where('event', $filters['event']);
        }

        if ($filters['search'] !== '') {
            $searchTerm = $filters['search'];
            $query->where(function (Builder $builder) use ($searchTerm): void {
                $builder
                    ->where('description', 'like', "%{$searchTerm}%")
                    ->orWhere('subject_label', 'like', "%{$searchTerm}%")
                    ->orWhere('event', 'like', "%{$searchTerm}%")
                    ->orWhereHas('user', function (Builder $userQuery) use ($searchTerm): void {
                        $userQuery->where('name', 'like', "%{$searchTerm}%");
                    });
            });
        }
    }

    private function buildStats(Builder $query): array
    {
        return [
            'total' => (clone $query)->count(),
            'actors' => (clone $query)->whereNotNull('user_id')->distinct('user_id')->count('user_id'),
            'created' => (clone $query)->where('event', 'created')->count(),
            'updated' => (clone $query)->whereIn('event', ['updated', 'profile_updated', 'password_updated', 'stock_updated'])->count(),
            'deleted' => (clone $query)->where('event', 'deleted')->count(),
        ];
    }

    /**
     * @return array{key:string, label:string, description:string, start:Carbon|null, end:Carbon|null}
     */
    private function resolveDateRange(?string $range): array
    {
        $rangeKey = in_array($range, ['day', 'week', 'month', 'all'], true) ? $range : 'day';
        $now = Carbon::now();

        return match ($rangeKey) {
            'week' => [
                'key' => 'week',
                'label' => 'This Week',
                'description' => $now->copy()->startOfWeek()->format('M d, Y').' - '.$now->copy()->endOfWeek()->format('M d, Y'),
                'start' => $now->copy()->startOfWeek(),
                'end' => $now->copy()->endOfWeek(),
            ],
            'month' => [
                'key' => 'month',
                'label' => 'This Month',
                'description' => $now->copy()->startOfMonth()->format('M d, Y').' - '.$now->copy()->endOfMonth()->format('M d, Y'),
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfMonth(),
            ],
            'all' => [
                'key' => 'all',
                'label' => 'All Time',
                'description' => 'All recorded activities',
                'start' => null,
                'end' => null,
            ],
            default => [
                'key' => 'day',
                'label' => 'Today',
                'description' => $now->copy()->format('M d, Y'),
                'start' => $now->copy()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
        };
    }
}

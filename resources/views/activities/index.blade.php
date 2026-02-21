<x-app-layout>
    @section('header', 'Activity History')

    @php
        $rangeOptions = [
            'day' => 'Today',
            'week' => 'This Week',
            'month' => 'This Month',
            'all' => 'All Time',
        ];
        $baseQuery = request()->except(['page', 'range']);
    @endphp

    <div class="max-w-7xl mx-auto space-y-8">
        <section class="relative overflow-hidden rounded-[2rem] bg-gradient-to-r from-slate-900 via-blue-900 to-cyan-800 p-8 text-white shadow-2xl shadow-blue-900/20">
            <div class="absolute -right-12 -top-12 h-48 w-48 rounded-full bg-cyan-400/20 blur-3xl"></div>
            <div class="absolute -bottom-16 left-10 h-56 w-56 rounded-full bg-blue-300/20 blur-3xl"></div>

            <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="space-y-2">
                    <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1 text-[11px] font-extrabold uppercase tracking-[0.22em]">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        Admin Only
                    </p>
                    <h1 class="text-3xl font-black tracking-tight">System Activity Timeline</h1>
                    <p class="text-sm font-medium text-blue-100">
                        {{ $rangeLabel }} - {{ $rangeDescription }}
                    </p>
                </div>

                <div class="rounded-2xl border border-white/20 bg-white/10 px-5 py-4 backdrop-blur">
                    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-blue-100">Current Records</p>
                    <p class="mt-1 text-3xl font-black">{{ number_format($stats['total']) }}</p>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-5">
            <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Total Events</p>
                <p class="mt-2 text-3xl font-black text-slate-900">{{ number_format($stats['total']) }}</p>
            </article>
            <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Active Users</p>
                <p class="mt-2 text-3xl font-black text-slate-900">{{ number_format($stats['actors']) }}</p>
            </article>
            <article class="rounded-2xl border border-emerald-100 bg-emerald-50/40 p-5 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-widest text-emerald-700">Created</p>
                <p class="mt-2 text-3xl font-black text-emerald-700">{{ number_format($stats['created']) }}</p>
            </article>
            <article class="rounded-2xl border border-blue-100 bg-blue-50/40 p-5 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-widest text-blue-700">Updated</p>
                <p class="mt-2 text-3xl font-black text-blue-700">{{ number_format($stats['updated']) }}</p>
            </article>
            <article class="rounded-2xl border border-rose-100 bg-rose-50/50 p-5 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-widest text-rose-700">Deleted</p>
                <p class="mt-2 text-3xl font-black text-rose-700">{{ number_format($stats['deleted']) }}</p>
            </article>
        </section>

        <section class="rounded-3xl border border-slate-100 bg-white p-6 shadow-xl shadow-slate-100/80">
            <div class="flex flex-col gap-5">
                <div class="flex flex-wrap items-center gap-2">
                    @foreach($rangeOptions as $key => $label)
                        <a href="{{ route('activities.index', array_merge($baseQuery, ['range' => $key])) }}"
                           class="rounded-xl border px-4 py-2 text-xs font-black uppercase tracking-wider transition {{ $filters['range'] === $key ? 'border-slate-900 bg-slate-900 text-white shadow-lg shadow-slate-200' : 'border-slate-200 bg-slate-50 text-slate-500 hover:bg-slate-100 hover:text-slate-800' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                <form method="GET" action="{{ route('activities.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-6">
                    <input type="hidden" name="range" value="{{ $filters['range'] }}">

                    <div class="xl:col-span-2">
                        <label for="search" class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-slate-400">Search</label>
                        <input id="search"
                               name="search"
                               type="text"
                               value="{{ $filters['search'] }}"
                               placeholder="User, action, subject..."
                               class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-medium text-slate-700 placeholder:text-slate-400 focus:border-slate-900 focus:ring-slate-900/10">
                    </div>

                    <div>
                        <label for="user_id" class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-slate-400">User</label>
                        <select id="user_id"
                                name="user_id"
                                class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-semibold text-slate-700 focus:border-slate-900 focus:ring-slate-900/10">
                            <option value="">All Users</option>
                            @foreach($actors as $actor)
                                <option value="{{ $actor->id }}" @selected((string) $filters['user_id'] === (string) $actor->id)>
                                    {{ $actor->name }} ({{ str_replace('_', ' ', $actor->role) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="event" class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-slate-400">Action</label>
                        <select id="event"
                                name="event"
                                class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-semibold text-slate-700 focus:border-slate-900 focus:ring-slate-900/10">
                            <option value="">All Actions</option>
                            @foreach($events as $event)
                                <option value="{{ $event }}" @selected($filters['event'] === $event)>
                                    {{ \Illuminate\Support\Str::headline(str_replace('_', ' ', (string) $event)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="per_page" class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-slate-400">Data Size</label>
                        <select id="per_page"
                                name="per_page"
                                class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-semibold text-slate-700 focus:border-slate-900 focus:ring-slate-900/10">
                            @foreach([10, 25, 50, 100] as $size)
                                <option value="{{ $size }}" @selected((int) $filters['per_page'] === $size)>
                                    {{ $size }} rows
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end gap-2 xl:justify-end">
                        <button type="submit"
                                class="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-black uppercase tracking-wider text-white transition hover:bg-black xl:w-auto">
                            Apply
                        </button>
                        <a href="{{ route('activities.index') }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-center text-xs font-black uppercase tracking-wider text-slate-600 transition hover:bg-slate-100 xl:w-auto">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-100 bg-white shadow-xl shadow-slate-100/80">
            <header class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-5">
                <h2 class="text-lg font-black text-slate-900">Detailed Activity Feed</h2>
                <p class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-bold uppercase tracking-widest text-slate-500">
                    {{ number_format($activities->total()) }} matching records
                </p>
            </header>

            <div class="space-y-4 p-6">
                @forelse($activities as $activity)
                    @php
                        $properties = is_array($activity->properties) ? $activity->properties : [];
                        $hasStateData = ! empty($properties['old']) || ! empty($properties['new']);
                        $subjectType = $activity->subject_type
                            ? \Illuminate\Support\Str::headline(class_basename((string) $activity->subject_type))
                            : 'System';
                        $eventBadgeClass = match ($activity->event) {
                            'created' => 'border-emerald-200 bg-emerald-100 text-emerald-700',
                            'deleted' => 'border-rose-200 bg-rose-100 text-rose-700',
                            'stock_updated' => 'border-amber-200 bg-amber-100 text-amber-700',
                            'password_updated' => 'border-fuchsia-200 bg-fuchsia-100 text-fuchsia-700',
                            'profile_updated' => 'border-indigo-200 bg-indigo-100 text-indigo-700',
                            default => 'border-blue-200 bg-blue-100 text-blue-700',
                        };
                    @endphp

                    <article class="rounded-2xl border border-slate-200 bg-gradient-to-r from-white to-slate-50/70 p-5 transition hover:-translate-y-0.5 hover:shadow-lg hover:shadow-slate-200/60">
                        <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                            <div class="space-y-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-[10px] font-black uppercase tracking-widest {{ $eventBadgeClass }}">
                                        {{ \Illuminate\Support\Str::headline(str_replace('_', ' ', (string) $activity->event)) }}
                                    </span>
                                    @if($activity->method)
                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                            {{ $activity->method }}
                                        </span>
                                    @endif
                                    <span class="rounded-full bg-cyan-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-cyan-700">
                                        {{ $subjectType }}
                                    </span>
                                </div>

                                <div>
                                    <h3 class="text-lg font-black text-slate-900">{{ $activity->description }}</h3>
                                    <p class="mt-1 text-sm font-medium text-slate-500">
                                        {{ $activity->user?->name ?? 'System' }}
                                        <span class="mx-1 text-slate-300">|</span>
                                        {{ $activity->created_at->format('M d, Y h:i A') }}
                                    </p>
                                </div>

                                <div class="grid gap-2 text-xs font-medium text-slate-500 sm:grid-cols-2">
                                    <p>
                                        <span class="font-bold text-slate-700">Target:</span>
                                        {{ $activity->subject_label ?: 'N/A' }}
                                    </p>
                                    <p>
                                        <span class="font-bold text-slate-700">Route:</span>
                                        {{ $activity->route_name ?: 'N/A' }}
                                    </p>
                                </div>
                            </div>

                            <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-xs text-slate-500 xl:min-w-[240px]">
                                <p class="font-bold uppercase tracking-wider text-slate-400">Request Info</p>
                                <p class="mt-2 break-all"><span class="font-bold text-slate-700">URL:</span> {{ $activity->url ?: 'N/A' }}</p>
                                <p class="mt-1"><span class="font-bold text-slate-700">IP:</span> {{ $activity->ip_address ?: 'N/A' }}</p>
                            </div>
                        </div>

                        @if($hasStateData)
                            <details class="group mt-4 overflow-hidden rounded-xl border border-slate-200 bg-slate-50/80">
                                <summary class="cursor-pointer list-none px-4 py-3 text-xs font-black uppercase tracking-wider text-slate-600">
                                    State Data
                                    <span class="ml-2 text-[10px] font-medium text-slate-400">Before and after values</span>
                                </summary>
                                <div class="grid gap-3 border-t border-slate-200 p-4 md:grid-cols-2">
                                    <div class="rounded-xl border border-rose-100 bg-rose-50/50 p-3">
                                        <p class="mb-2 text-[10px] font-black uppercase tracking-widest text-rose-700">Before</p>
                                        <pre class="max-h-56 overflow-auto whitespace-pre-wrap break-all rounded-lg bg-white p-3 text-[11px] font-medium text-slate-600">{{ json_encode($properties['old'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    </div>
                                    <div class="rounded-xl border border-emerald-100 bg-emerald-50/50 p-3">
                                        <p class="mb-2 text-[10px] font-black uppercase tracking-widest text-emerald-700">After</p>
                                        <pre class="max-h-56 overflow-auto whitespace-pre-wrap break-all rounded-lg bg-white p-3 text-[11px] font-medium text-slate-600">{{ json_encode($properties['new'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    </div>
                                </div>
                            </details>
                        @endif
                    </article>
                @empty
                    <div class="flex min-h-56 flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-slate-50 text-center">
                        <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-200 text-slate-500">
                            <i class="fas fa-clock-rotate-left text-xl"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-600">No activity data for this filter.</p>
                        <p class="mt-1 text-xs text-slate-400">Try changing range, user, or action type.</p>
                    </div>
                @endforelse
            </div>

            @if($activities->hasPages())
                <footer class="border-t border-slate-100 px-6 py-4">
                    {{ $activities->links() }}
                </footer>
            @endif
        </section>
    </div>
</x-app-layout>

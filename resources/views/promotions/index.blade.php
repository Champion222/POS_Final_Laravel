<x-app-layout>
    @section('header', 'Promotions')

    @php
        $canManagePromotions = in_array(auth()->user()->role, ['admin', 'stock_manager']);
    @endphp

    <div
        x-data="{
            deleteModalOpen: false,
            deleteAction: '',
            deleteName: '',
            openDeleteModal(action, name) {
                this.deleteAction = action;
                this.deleteName = name;
                this.deleteModalOpen = true;
            },
            closeDeleteModal() {
                this.deleteModalOpen = false;
                this.deleteAction = '';
                this.deleteName = '';
            }
        }"
        class="max-w-7xl mx-auto space-y-8"
    >
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
            <div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-emerald-500 via-emerald-600 to-teal-700 p-6 text-white shadow-2xl shadow-emerald-200">
                <div class="relative z-10">
                    <p class="text-emerald-100 text-xs font-bold uppercase tracking-widest">Active Promotions</p>
                    <h3 class="text-4xl font-black mt-3">{{ $activeCount }}</h3>
                    <p class="text-xs text-emerald-100 mt-2">Running right now</p>
                </div>
                <div class="absolute -bottom-8 -right-6 text-8xl opacity-10">
                    <i class="fas fa-bolt"></i>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-[2rem] bg-white p-6 border border-indigo-100 shadow-xl shadow-indigo-100/40">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-indigo-400">Scheduled</p>
                        <h3 class="text-4xl font-black text-gray-900 mt-3">{{ $scheduledCount }}</h3>
                        <p class="text-xs text-gray-400 mt-2">Upcoming launches</p>
                    </div>
                    <div class="h-12 w-12 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-[2rem] bg-white p-6 border border-red-100 shadow-xl shadow-red-100/40">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-red-400">Expired</p>
                        <h3 class="text-4xl font-black text-gray-900 mt-3">{{ $expiredCount }}</h3>
                        <p class="text-xs text-gray-400 mt-2">Past campaigns</p>
                    </div>
                    <div class="h-12 w-12 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center">
                        <i class="fas fa-hourglass-end"></i>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-[2rem] bg-slate-900 p-6 text-white shadow-2xl shadow-slate-900/30">
                <div class="relative z-10">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-300">Promoted Products</p>
                    <h3 class="text-4xl font-black mt-3">{{ $promotedProducts }}</h3>
                    <p class="text-xs text-slate-300 mt-2">Active discounts</p>
                </div>
                <div class="absolute right-0 top-0 h-24 w-24 rounded-full bg-indigo-500/20 blur-2xl"></div>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-100/60 border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-1 rounded-full bg-indigo-500"></div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Promotion List</h3>
                        <p class="text-sm text-gray-400">Control discounts across your inventory.</p>
                        @unless($canManagePromotions)
                            <p class="text-xs font-bold text-indigo-500 uppercase tracking-widest mt-2">View Only</p>
                        @endunless
                    </div>
                </div>
                @if($canManagePromotions)
                    <a href="{{ route('promotions.create') }}" class="group bg-gray-900 hover:bg-black text-white px-6 py-3.5 rounded-2xl font-bold shadow-lg shadow-gray-300 transition flex items-center gap-3">
                        <span class="inline-flex items-center justify-center h-7 w-7 rounded-xl bg-white/10">
                            <i class="fas fa-plus text-xs"></i>
                        </span>
                        <span>New Promotion</span>
                    </a>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50/60 text-gray-400 font-bold uppercase text-[10px] tracking-wider border-b border-gray-50">
                        <tr>
                            <th class="px-8 py-5">Promotion</th>
                            <th class="px-6 py-5">Discount</th>
                            <th class="px-6 py-5">Dates</th>
                            <th class="px-6 py-5">Status</th>
                            <th class="px-6 py-5 text-center">Products</th>
                            @if($canManagePromotions)
                                <th class="px-8 py-5 text-right">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($promotions as $promotion)
                            @php
                                $discountLabel = $promotion->type === 'percent'
                                    ? rtrim(rtrim(number_format($promotion->discount_value, 2), '0'), '.') . '%'
                                    : '$' . number_format($promotion->discount_value, 2);
                                $isExpired = $promotion->end_date?->isPast() ?? false;
                                $isScheduled = $promotion->start_date?->isFuture() ?? false;
                                $isLive = $promotion->is_active && ! $isExpired && ! $isScheduled;
                                $statusText = $isExpired ? 'Expired' : ($isScheduled ? 'Scheduled' : ($promotion->is_active ? 'Live' : 'Paused'));
                            @endphp
                            <tr class="group hover:bg-indigo-50/10 transition-colors">
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="h-12 w-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                            <i class="fas fa-percent"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 text-base group-hover:text-indigo-600 transition-colors">{{ $promotion->name }}</p>
                                            <p class="text-xs text-gray-400 font-medium uppercase tracking-widest">{{ $promotion->type === 'percent' ? 'Percent' : 'Fixed' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-100 text-xs font-bold">
                                        {{ $discountLabel }} OFF
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="text-xs font-bold text-gray-500">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-play text-gray-300"></i>
                                            <span>{{ $promotion->start_date?->format('M d, Y') }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 mt-2">
                                            <i class="fas fa-flag-checkered text-gray-300"></i>
                                            <span>{{ $promotion->end_date?->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    @if($isLive)
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-extrabold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> {{ $statusText }}
                                        </span>
                                    @elseif($isScheduled)
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-extrabold bg-indigo-50 text-indigo-600 border border-indigo-100">
                                            <span class="w-2 h-2 rounded-full bg-indigo-400"></span> {{ $statusText }}
                                        </span>
                                    @elseif($isExpired)
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-extrabold bg-red-50 text-red-600 border border-red-100">
                                            <span class="w-2 h-2 rounded-full bg-red-500"></span> {{ $statusText }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-extrabold bg-gray-100 text-gray-500 border border-gray-200">
                                            <span class="w-2 h-2 rounded-full bg-gray-400"></span> {{ $statusText }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[3rem] px-3 py-1.5 rounded-xl text-xs font-bold bg-gray-50 text-gray-600 border border-gray-100">
                                        {{ $promotion->products_count }}
                                    </span>
                                </td>
                                @if($canManagePromotions)
                                    <td class="px-8 py-5 text-right">
                                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all duration-200">
                                            <a href="{{ route('promotions.edit', $promotion) }}" class="h-9 w-9 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-400 hover:text-indigo-600 hover:border-indigo-200 hover:shadow-sm transition-all" title="Edit">
                                                <i class="fas fa-pen text-xs"></i>
                                            </a>
                                            <button
                                                type="button"
                                                @click="openDeleteModal(@js(route('promotions.destroy', $promotion)), @js($promotion->name))"
                                                class="h-9 w-9 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-400 hover:text-red-600 hover:border-red-200 hover:bg-red-50 transition-all"
                                                title="Delete"
                                            >
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $canManagePromotions ? 6 : 5 }}" class="px-8 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="h-20 w-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-tags text-3xl text-gray-300"></i>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900">No Promotions Yet</h3>
                                        <p class="text-gray-400 text-sm mt-1">Create your first campaign to boost sales.</p>
                                        @if($canManagePromotions)
                                            <a href="{{ route('promotions.create') }}" class="mt-4 text-indigo-600 font-bold hover:underline">Create a Promotion</a>
                                        @else
                                            <p class="mt-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Ask Admin to Add</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($canManagePromotions)
            <div
                x-show="deleteModalOpen"
                x-cloak
                @keydown.escape.window="closeDeleteModal()"
                class="relative z-[60]"
                aria-labelledby="delete-promotion-title"
                role="dialog"
                aria-modal="true"
            >
                <div
                    x-show="deleteModalOpen"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"
                    @click="closeDeleteModal()"
                ></div>

                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center">
                        <div
                            x-show="deleteModalOpen"
                            @click.away="closeDeleteModal()"
                            x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="ease-in duration-200"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left shadow-2xl transition-all"
                        >
                            <div class="text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-50 text-red-500">
                                    <i class="fas fa-trash-alt text-xl"></i>
                                </div>
                                <h3 id="delete-promotion-title" class="mt-4 text-lg font-bold text-gray-900">Delete promotion?</h3>
                                <p class="mt-2 text-sm text-gray-500">
                                    This will permanently remove
                                    <span class="font-semibold text-gray-800" x-text="deleteName || 'this promotion'"></span>.
                                </p>
                            </div>

                            <div class="mt-6 flex gap-3">
                                <button
                                    type="button"
                                    @click="closeDeleteModal()"
                                    class="w-full rounded-xl bg-gray-100 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-200 transition"
                                >
                                    Cancel
                                </button>
                                <form method="POST" :action="deleteAction" class="w-full">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full rounded-xl bg-red-600 py-2.5 text-sm font-bold text-white shadow-lg shadow-red-200 transition hover:bg-red-700">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>

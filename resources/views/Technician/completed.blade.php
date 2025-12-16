@extends('layouts.app')

@php
    $pageTitle = 'Completed Tasks';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'Technician Dashboard', 'url' => route('technician.dashboard')],
        ['label' => 'Completed Tasks'],
    ];
    $urgColors = [
        'high' => ['#E74C3C', '#FFFFFF'],
        'medium' => ['#F1C40F', '#2C3E50'],
        'low' => ['#2ECC71', '#FFFFFF'],
    ];
@endphp

@section('content')
    <section class="space-y-4" x-data="completedPage()" x-init="load()">
        <div class="rounded-2xl shadow-sm border border-[#D7DDE5] bg-white p-4 space-y-4">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-[#2C3E50]">Completed Tasks</h1>
                    <p class="text-sm text-[#7F8C8D]">All tasks you've closed, with filters and history.</p>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-transparent bg-gradient-to-r from-[#1F4E79] to-[#3498DB] p-4 text-white shadow">
                    <div class="text-sm opacity-80">Completed tasks</div>
                    <div class="text-2xl font-semibold mt-1" x-text="summary.total"></div>
                    <p class="text-xs opacity-80 mt-2">Total jobs closed in the selected window.</p>
                </div>
                <div class="rounded-2xl border border-[#D7DDE5] bg-gradient-to-r from-[#27AE60] to-[#2ECC71] p-4 text-white shadow">
                    <div class="text-sm opacity-80">High urgency</div>
                    <div class="text-2xl font-semibold mt-1" x-text="summary.high_urgency || 0"></div>
                    <p class="text-xs opacity-80 mt-2">Closed high urgency tasks.</p>
                </div>
                <div class="rounded-2xl border border-[#D7DDE5] bg-gradient-to-r from-[#34495E] to-[#2C3E50] p-4 text-white shadow">
                    <div class="text-sm opacity-80">Avg completion time</div>
                    <div class="text-2xl font-semibold mt-1" x-text="summary.avg_time || '-'"></div>
                    <p class="text-xs opacity-80 mt-2">Time spent per task (avg).</p>
                </div>
            </div>

            <form class="grid gap-3 lg:grid-cols-5 items-center rounded-xl bg-[#F8FBFF] border border-[#D7DDE5] p-3 shadow-sm" @submit.prevent="load">
                <input type="date" x-model="filters.from" class="rounded-lg px-3 py-2 border border-[#D7DDE5] bg-white text-[#2C3E50]" />
                <input type="date" x-model="filters.to" class="rounded-lg px-3 py-2 border border-[#D7DDE5] bg-white text-[#2C3E50]" />
                <select x-model="filters.category" class="rounded-lg px-3 py-2 border border-[#D7DDE5] bg-white text-[#2C3E50]">
                    <option value="">All Categories</option>
                    <template x-for="cat in categories" :key="cat.id">
                        <option :value="cat.id" x-text="cat.name"></option>
                    </template>
                </select>
                <select x-model="filters.block" class="rounded-lg px-3 py-2 border border-[#D7DDE5] bg-white text-[#2C3E50]">
                    <option value="">Block</option>
                    <option>Block A</option>
                    <option>Block B</option>
                    <option>Block C</option>
                    <option>Block M</option>
                </select>
                <div class="grid sm:grid-cols-2 lg:grid-cols-1 gap-3">
                    <input type="text" x-model="filters.q" placeholder="Search notes" class="rounded-lg px-3 py-2 border border-[#D7DDE5] bg-white text-[#2C3E50]" />
                    <button type="submit" class="rounded-lg px-4 py-2 font-semibold bg-[#1F4E79] text-white shadow-sm">Apply</button>
                </div>
            </form>

            <div id="history" class="overflow-x-auto mt-2">
                <table class="min-w-full text-sm rounded-xl overflow-hidden border border-[#D7DDE5]">
                    <thead>
                        <tr class="bg-[#F5F7FA] text-[#2C3E50]">
                            <th class="text-left px-3 py-2">ID</th>
                            <th class="text-left px-3 py-2">Room</th>
                            <th class="text-left px-3 py-2">Block</th>
                            <th class="text-left px-3 py-2">Category</th>
                            <th class="text-left px-3 py-2">Description</th>
                            <th class="text-left px-3 py-2">Urgency</th>
                            <th class="text-left px-3 py-2">Status</th>
                            <th class="text-left px-3 py-2">Completion Due</th>
                            <th class="text-left px-3 py-2">Resolution Notes</th>
                            <th class="text-left px-3 py-2">Reported At</th>
                            <th class="text-left px-3 py-2">Due At</th>
                            <th class="text-left px-3 py-2">Completed At</th>
                            <th class="text-left px-3 py-2">Duration</th>
                            <th class="text-left px-3 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-[#2C3E50] divide-y divide-[#D7DDE5]">
                        <template x-if="loading">
                            <tr><td colspan="14" class="px-3 py-4 text-center text-[#7F8C8D]">Loading...</td></tr>
                        </template>
                        <template x-if="!loading && rows.length === 0">
                            <tr><td colspan="14" class="px-3 py-4 text-center text-[#7F8C8D]">No completed tasks found for the selected filters.</td></tr>
                        </template>
                        <template x-for="row in rows" :key="row.id">
                            <tr class="bg-white">
                                <td class="px-3 py-2 font-semibold text-[#1F4E79]" x-text="row.id"></td>
                                <td class="px-3 py-2" x-text="row.room"></td>
                                <td class="px-3 py-2" x-text="row.block"></td>
                                <td class="px-3 py-2" x-text="row.category"></td>
                                <td class="px-3 py-2 text-[#2C3E50]" x-text="row.description"></td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold" :style="urgStyle(row.urgency)" x-text="row.urgency"></span>
                                </td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold" style="background:#27AE60;color:#FFFFFF;" x-text="row.status"></span>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <template x-if="row.is_overdue">
                                        <span class="px-3 py-2 rounded-2xl text-xs font-semibold inline-block"
                                            style="background:#E74C3C;color:#FFFFFF;"
                                            x-text="row.overdue_label"></span>
                                    </template>
                                    <template x-if="!row.is_overdue">
                                        <span class="px-3 py-2 rounded-2xl text-xs font-semibold inline-block"
                                            style="background:#E8F8F0;color:#1E8449;border:1px solid #BFE5D0;">
                                            On time
                                        </span>
                                    </template>
                                </td>
                                <td class="px-3 py-2 text-[#7F8C8D]" x-text="row.resolution_notes"></td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div x-text="row.report_date"></div>
                                    <div class="text-xs text-[#7F8C8D]" x-text="row.report_time"></div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div x-text="row.due_date"></div>
                                    <div class="text-xs text-[#7F8C8D]" x-text="row.due_time"></div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div x-text="row.completed_date"></div>
                                    <div class="text-xs text-[#7F8C8D]" x-text="row.completed_time"></div>
                                </td>
                                <td class="px-3 py-2" x-text="row.duration"></td>
                                <td class="px-3 py-2">
                                    <a :href="`/technician/tasks/${row.id}`" class="text-sm font-semibold text-[#1F4E79] underline">View</a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex items-center gap-3 justify-end" x-show="pagination.total > 0">
                <button class="px-3 py-1 rounded border border-[#D7DDE5] text-sm"
                    :disabled="pagination.current_page <= 1"
                    @click="changePage(pagination.current_page - 1)">
                    Prev
                </button>
                <div class="text-sm text-[#2C3E50]">
                    Page <span x-text="pagination.current_page"></span> / <span x-text="pagination.last_page"></span>
                </div>
                <button class="px-3 py-1 rounded border border-[#D7DDE5] text-sm"
                    :disabled="pagination.current_page >= pagination.last_page"
                    @click="changePage(pagination.current_page + 1)">
                    Next
                </button>
            </div>
        </div>
    </section>

    <script>
        function completedPage() {
            return {
                summary: { total: 0, avg_time: '-', high_urgency: 0 },
                rows: [],
                categories: [],
                pagination: { current_page: 1, last_page: 1, total: 0 },
                filters: { from: '', to: '', category: '', block: '', q: '' },
                loading: false,
                urgStyle(u) {
                    const map = {
                        high: ['#E74C3C', '#FFFFFF'],
                        medium: ['#F1C40F', '#2C3E50'],
                        low: ['#2ECC71', '#FFFFFF'],
                    };
                    const key = (u || '').toLowerCase();
                    const [bg, fg] = map[key] || ['#D7DDE5', '#2C3E50'];
                    return `background:${bg};color:${fg};`;
                },
                buildUrl(page = 1) {
                    const params = new URLSearchParams();
                    Object.entries(this.filters).forEach(([k, v]) => { if (v) params.append(k, v); });
                    params.append('page', page);
                    return `/api/tech/completed?${params.toString()}`;
                },
                async load(page = 1) {
                    this.loading = true;
                    try {
                        const res = await fetch(this.buildUrl(page), { credentials: 'same-origin' });
                        if (!res.ok) throw new Error('Failed to load');
                        const json = await res.json();
                        const data = json.data || {};
                        this.summary = data.summary || this.summary;
                        const humanize = (ms) => {
                            if (!ms || ms < 0) return '';
                            const h = Math.floor(ms / 3600000);
                            const m = Math.floor((ms % 3600000) / 60000);
                            if (h > 0) return `${h}h ${m}m`;
                            return `${m}m`;
                        };
                        const toParts = (val) => {
                            if (!val || val === '-') return { date: '-', time: '' };
                            const [d, t] = val.split(' ');
                            return { date: d || '-', time: t || '' };
                        };
                        this.rows = (data.rows || []).map(row => {
                            const r = toParts(row.report_at);
                            const d = toParts(row.due_at);
                            const c = toParts(row.completed_at);

                            // compute lateness vs due (completed tasks only)
                            let overdueLabel = 'On time';
                            let isOverdue = false;
                            if (row.completed_at && row.due_at && row.completed_at !== '-' && row.due_at !== '-') {
                                const due = new Date(row.due_at.replace(' ', 'T'));
                                const comp = new Date(row.completed_at.replace(' ', 'T'));
                                if (!isNaN(due) && !isNaN(comp) && comp > due) {
                                    const diffMs = comp - due;
                                    const label = humanize(diffMs);
                                    overdueLabel = label ? `Late by ${label}` : 'Late';
                                    isOverdue = true;
                                }
                            }

                            return {
                                ...row,
                                report_date: r.date,
                                report_time: r.time,
                                due_date: d.date,
                                due_time: d.time,
                                completed_date: c.date,
                                completed_time: c.time,
                                is_overdue: isOverdue,
                                overdue_label: overdueLabel,
                            };
                        });
                        this.pagination = data.pagination || this.pagination;
                        this.categories = data.categories || [];
                    } catch (e) {
                        console.error(e);
                        this.rows = [];
                        this.pagination = { current_page: 1, last_page: 1, total: 0 };
                    } finally {
                        this.loading = false;
                    }
                },
                changePage(p) {
                    if (p < 1 || p > this.pagination.last_page) return;
                    this.load(p);
                },
            };
        }
    </script>
@endsection

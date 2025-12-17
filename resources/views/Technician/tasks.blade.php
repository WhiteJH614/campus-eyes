@extends('layouts.app')

@php
    $pageTitle = 'Assigned Jobs';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'Technician Dashboard', 'url' => route('technician.dashboard')],
        ['label' => 'Assigned Jobs'],
    ];
@endphp

@section('content')
    <section class="space-y-6" x-data="tasksPage()" x-init="load()">
        <div class="rounded-2xl shadow-sm border border-[#D7DDE5] bg-white p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-[#2C3E50]">Assigned Jobs</h1>
                    <p class="text-sm text-[#7F8C8D]">
                        Jobs currently assigned to you. Filter and review task details.
                    </p>
                </div>
            </div>

            {{-- Filters --}}
            <form class="mt-4 grid gap-3 lg:grid-cols-5" @submit.prevent="load">
                <input type="text" x-model="filters.q" placeholder="Search by Report ID"
                    class="rounded-lg px-3 py-2 border border-[#D7DDE5] bg-white text-[#2C3E50]" />

                <select x-model="filters.status" class="rounded-lg px-3 py-2 border border-[#D7DDE5] bg-white text-[#2C3E50]">
                    <option value="">Status</option>
                    <template x-for="s in ['Assigned','In_Progress','Overdue']" :key="s">
                        <option :value="s" x-text="s.replace('_',' ')"></option>
                    </template>
                </select>

                <select x-model="filters.urgency" class="rounded-lg px-3 py-2 border border-[#D7DDE5] bg-white text-[#2C3E50]">
                    <option value="">Urgency</option>
                    <template x-for="u in ['High','Medium','Low']" :key="u">
                        <option :value="u" x-text="u"></option>
                    </template>
                </select>

                <select x-model="filters.sort" class="rounded-lg px-3 py-2 border border-[#D7DDE5] bg-white text-[#2C3E50]">
                    <option value="due">Sort: Due date (default)</option>
                    <option value="urgency">Sort: Urgency</option>
                    <option value="block">Sort: Block</option>
                </select>

                <button type="submit" class="rounded-lg px-4 py-2 font-semibold bg-[#1F4E79] text-white">
                    Apply
                </button>
            </form>

            {{-- Jobs table --}}
            <div class="overflow-x-auto mt-4">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-[#F5F7FA] text-[#2C3E50]">
                            <th class="text-left px-3 py-2">Report ID</th>
                            <th class="text-left px-3 py-2">Date reported</th>
                            <th class="text-left px-3 py-2">Location</th>
                            <th class="text-left px-3 py-2">Category</th>
                            <th class="text-left px-3 py-2">Urgency</th>
                            <th class="text-left px-3 py-2">Status</th>
                            <th class="text-left px-3 py-2">Overdue</th>
                            <th class="text-left px-3 py-2">Due date</th>
                            <th class="text-left px-3 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-[#2C3E50]">
                        <template x-if="loading">
                            <tr>
                                <td colspan="9" class="px-3 py-4 text-center text-[#7F8C8D]">Loading...</td>
                            </tr>
                        </template>
                        <template x-if="!loading && jobs.length === 0">
                            <tr>
                                <td colspan="9" class="px-3 py-4 text-center text-[#7F8C8D]">
                                    No assigned jobs found.
                                </td>
                            </tr>
                        </template>
                        <template x-for="job in jobs" :key="job.id">
                            <tr class="border-t border-[#D7DDE5] hover:bg-[#F9FBFF]">
                                <td class="px-3 py-2 font-semibold text-[#1F4E79]">
                                    <a :href="`/technician/tasks/${job.id}`" x-text="job.id"></a>
                                </td>
                                <td class="px-3 py-2" x-text="job.reported_at"></td>
                                <td class="px-3 py-2" x-text="job.location || '-'"></td>
                                <td class="px-3 py-2" x-text="job.category"></td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold inline-block w-full text-center"
                                        :style="urgencyStyle(job.urgency)">
                                        <span x-text="job.urgency || 'N/A'"></span>
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold inline-block w-full text-center"
                                        :style="statusStyle(job.status)">
                                        <span x-text="job.status.replace('_',' ')"></span>
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="inline-flex items-center gap-2 px-2 py-1 rounded-full text-xs font-semibold w-full justify-center"
                                        :style="job.is_overdue ? 'background:#FDECEA;color:#C0392B;border:1px solid #F5C6CB;' : 'background:#E8F8F0;color:#1E8449;border:1px solid #BFE5D0;'">
                                        <!-- <span
                                            :style="job.is_overdue ? 'background:#E74C3C;color:#FFFFFF;' : 'background:#27AE60;color:#FFFFFF;'"
                                            class="h-6 w-6 rounded-full inline-flex items-center justify-center text-[10px] font-bold">
                                            <template x-if="job.is_overdue">!</template>
                                            <template x-if="!job.is_overdue">✓</template>
                                        </span> -->
                                        <div class="flex flex-col leading-tight text-left">
                                            <span x-text="job.is_overdue ? 'Overdue' : 'On track'"></span>
                                            <span class="text-[11px] font-normal"
                                                x-text="job.is_overdue && job.overdue_human_display ? job.overdue_human_display : ''"></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2" x-text="job.due_at"></td>
                                <td class="px-3 py-2">
                                    <a :href="`/technician/tasks/${job.id}`"
                                        class="text-xs font-semibold text-[#1F4E79]">
                                        View
                                    </a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

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
        </div>
    </section>

    <script>
        function tasksPage() {
            return {
                jobs: [],
                pagination: { current_page: 1, last_page: 1, total: 0 },
                filters: { q: '', status: '', urgency: '', sort: 'due' },
                loading: false,
                urgencyStyle(u) {
                    const map = {
                        high: ['#E74C3C', '#FFFFFF'],
                        medium: ['#F1C40F', '#2C3E50'],
                        low: ['#2ECC71', '#FFFFFF'],
                    };
                    const key = (u || '').toLowerCase();
                    const [bg, fg] = map[key] || ['#D7DDE5', '#2C3E50'];
                    return `background-color:${bg};color:${fg};`;
                },
                statusStyle(s) {
                    const map = {
                        Pending: ['#95A5A6', '#FFFFFF'],
                        Assigned: ['#F39C12', '#FFFFFF'],
                        In_Progress: ['#3498DB', '#FFFFFF'],
                        Completed: ['#27AE60', '#FFFFFF'],
                    };
                    const [bg, fg] = map[s] || ['#D7DDE5', '#2C3E50'];
                    return `background-color:${bg};color:${fg};`;
                },
                buildUrl(page = 1) {
                    const params = new URLSearchParams();
                    if (this.filters.q) params.append('q', this.filters.q);
                    if (this.filters.status) params.append('status', this.filters.status);
                    if (this.filters.urgency) params.append('urgency', this.filters.urgency);
                    if (this.filters.sort) params.append('sort', this.filters.sort);
                    params.append('page', page);
                    return `/api/tech/tasks?${params.toString()}`;
                },
                async load(page = 1) {
                    this.loading = true;
                    try {
                        const res = await fetch(this.buildUrl(page), { credentials: 'same-origin' });
                        if (!res.ok) throw new Error('Failed to load tasks');
                        const json = await res.json();
                        const data = json.data || {};
                        const humanize = (ms) => {
                            if (!ms || ms < 0) return '';
                            const h = Math.floor(ms / 3600000);
                            const m = Math.floor((ms % 3600000) / 60000);
                            if (h > 0) return `${h}h ${m}m`;
                            return `${m}m`;
                        };

                        this.jobs = (data.jobs || []).map(j => {
                            let overdueDisplay = '';
                            if (j.is_overdue && j.due_at) {
                                const due = new Date(j.due_at.replace(' ', 'T'));
                                const now = new Date();
                                const diff = now - due;
                                overdueDisplay = diff > 0 ? humanize(diff) : '';
                            }
                            return {
                                ...j,
                                overdue_human_display: overdueDisplay,
                            };
                        });
                        this.pagination = data.pagination || { current_page: 1, last_page: 1, total: 0 };
                    } catch (e) {
                        console.error(e);
                        this.jobs = [];
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

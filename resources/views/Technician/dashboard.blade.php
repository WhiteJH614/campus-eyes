<!-- author: Lee Jia Hui -->
@extends('layouts.app')

@php
    $pageTitle = 'Technician Dashboard';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'Technician Dashboard'],
    ];
@endphp

@section('content')
    <section class="space-y-8" x-data="dashboardPage()" x-init="load()">
        <div
            class="rounded-2xl border border-transparent bg-gradient-to-r from-[#102A43] via-[#1F4E79] to-[#2A7ABF] text-white p-6 shadow-lg">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold">Technician Dashboard</h1>
                    <p class="text-sm text-white/80">Monitor workload, overdue items, and recent updates.</p>
                </div>

                {{-- Quick filters to technician jobs --}}
                <div class="flex flex-wrap gap-2 text-sm">
                    <a href="{{ route('technician.tasks') }}"
                        class="px-3 py-2 rounded-lg bg-white text-[#1F4E79] font-semibold shadow-sm">
                        All Tasks
                    </a>
                    <a href="{{ route('technician.tasks', ['urgency' => 'High']) }}"
                        class="px-3 py-2 rounded-lg bg-[#E74C3C] text-white font-semibold shadow-sm">
                        High Urgency
                    </a>
                    <a href="{{ route('technician.tasks', ['status' => 'In_Progress']) }}"
                        class="px-3 py-2 rounded-lg bg-[#3498DB] text-white font-semibold shadow-sm">
                        In Progress
                    </a>
                    <a href="{{ route('technician.tasks', ['status' => 'Overdue']) }}"
                        class="px-3 py-2 rounded-lg bg-[#F39C12] text-white font-semibold shadow-sm">
                        Overdue
                    </a>
                </div>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-4" x-show="!loading">
            <template x-for="stat in stats" :key="stat.label">
                <div class="rounded-xl p-4 text-white shadow-md" :class="colorFor(stat.label)">
                    <div class="text-sm" style="color:rgba(255,255,255,0.8);" x-text="stat.label"></div>
                    <div class="text-3xl font-semibold mt-1" x-text="stat.value"></div>
                </div>
            </template>
        </div>
        <div x-show="loading" class="text-sm text-[#7F8C8D]">Loading dashboard...</div>

        <div class="rounded-2xl border border-[#F39C12] bg-[#FCEED3] px-5 py-4 shadow-sm">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="font-semibold text-[#7F3C0A] text-lg">Overdue tasks</div>
                    <p class="text-sm text-[#7F3C0A]">
                        You have <span x-text="overdueCount"></span> overdue task<span
                            x-text="overdueCount === 1 ? '' : 's'"></span>. Focus on these first.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2 text-sm">
                    <a href="{{ route('technician.tasks', ['status' => 'Overdue']) }}"
                        class="px-3 py-2 rounded-full bg-[#F39C12] text-white font-semibold shadow-sm">
                        View overdue
                    </a>

                    <template x-if="nextOverdue">
                        <a :href="`/technician/tasks/${nextOverdue.id}`"
                            class="px-3 py-2 rounded-full bg-[#1F4E79] text-white font-semibold shadow-sm">
                            Next task →
                        </a>
                    </template>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Placeholder for chart / analytics --}}
            <div class="lg:col-span-2 rounded-2xl shadow-sm border border-[#D7DDE5] bg-white p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-[#2C3E50]">Tasks by status</h2>
                    <a href="{{ route('technician.tasks') }}" class="text-sm font-semibold text-[#1F4E79]">
                        View tasks
                    </a>
                </div>
                <div
                    class="h-56 bg-gradient-to-br from-[#F5F7FA] to-[#E8EEF7] rounded-xl flex items-center justify-center border border-dashed border-[#D7DDE5]">
                    <canvas id="statusChart" class="w-full h-full"></canvas>
                </div>
            </div>

            {{-- Recent activity --}}
            <div class="rounded-2xl shadow-sm border border-[#D7DDE5] bg-white p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-[#2C3E50]">Recent activity</h2>
                </div>
                <ul class="space-y-3 text-sm text-[#2C3E50]">
                    <template x-if="recent.length === 0">
                        <li class="text-sm text-[#7F8C8D]">No recent activity yet.</li>
                    </template>
                    <template x-for="(item, idx) in recent" :key="idx">
                        <li class="rounded-lg px-3 py-2 bg-[#F5F7FA]">
                            <span class="whitespace-pre-line" x-text="item"></span>
                        </li>
                    </template>
                </ul>
            </div>
        </div>
    </section>

    <script>
        function dashboardPage() {
            return {
                stats: [],
                overdueCount: 0,
                recent: [],
                nextOverdue: null,
                loading: true,
                chart: null,
                colorFor(label) {
                    const map = {
                        'Assigned': 'bg-[#1F4E79]',
                        'In Progress': 'bg-[#3498DB]',
                        'Completed (month)': 'bg-[#27AE60]',
                        'Overdue': 'bg-[#F39C12]',
                    };
                    return map[label] || 'bg-[#2C3E50]';
                },
                async load() {
                    try {
                        const res = await fetch('/api/technician/dashboard', { credentials: 'same-origin' });
                        if (!res.ok) throw new Error('Failed to load dashboard');
                        const json = await res.json();
                        const data = json.data || {};
                        this.stats = data.stats || [];
                        this.overdueCount = data.overdueCount || 0;
                        this.recent = data.recent || [];
                        this.nextOverdue = data.nextOverdue || null;
                        this.renderChart();
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.loading = false;
                    }
                },
                renderChart() {
                    const canvas = document.getElementById('statusChart');
                    if (!canvas) return;
                    const ctx = canvas.getContext('2d');
                    const labels = this.stats.map(s => s.label);
                    const values = this.stats.map(s => s.value);
                    const colors = this.stats.map(s => {
                        const cls = this.colorFor(s.label);
                        // Extract hex from bg-[...]
                        const match = cls.match(/#([0-9A-Fa-f]{6})/);
                        return match ? `#${match[1]}` : '#1F4E79';
                    });

                    // Clear
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    // Responsive sizing
                    const width = canvas.clientWidth;
                    const height = canvas.clientHeight;
                    canvas.width = width;
                    canvas.height = height;

                    const padding = 32;
                    const barWidth = (width - padding * 2) / (values.length * 2);
                    const maxVal = Math.max(...values, 1);
                    values.forEach((val, idx) => {
                        const x = padding + idx * barWidth * 2 + barWidth / 2;
                        const barHeight = (val / maxVal) * (height - padding * 2);
                        const y = height - padding - barHeight;
                        ctx.fillStyle = colors[idx];
                        ctx.beginPath();
                        ctx.roundRect(x, y, barWidth, barHeight, 6);
                        ctx.fill();
                        // value
                        ctx.fillStyle = '#000000';
                        ctx.font = '12px sans-serif';
                        ctx.textAlign = 'center';
                        ctx.fillText(val, x + barWidth / 2, y - 6);
                        // label horizontal under bar
                        ctx.fillStyle = '#000000';
                        ctx.textBaseline = 'top';
                        ctx.fillText(labels[idx], x + barWidth / 2, height - padding + 6);
                    });
                },
            };
        }
    </script>
@endsection
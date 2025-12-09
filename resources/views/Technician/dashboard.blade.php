@extends('layouts.app')

@php
$pageTitle = 'Technician Dashboard';
$breadcrumbs = [
    ['label' => 'Home', 'url' => '/'],
    ['label' => 'Technician Dashboard'],
];
@endphp

@section('content')
    <section class="space-y-8">
        <div class="rounded-2xl shadow-sm border border-[#D7DDE5] bg-white p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-[#2C3E50]">Technician Dashboard</h1>
                    <p class="text-sm text-[#7F8C8D]">
                        Monitor your workload, overdue items, and recent updates.
                    </p>
                </div>

                {{-- Quick filters to technician jobs --}}
                <div class="flex flex-wrap gap-2 text-sm">
                    <a href="{{ route('technician.my_jobs') }}"
                        class="px-3 py-2 rounded-lg border border-[#D7DDE5] bg-white text-[#1F4E79]">
                        All Tasks
                    </a>
                    <a href="{{ route('technician.my_jobs', ['urgency' => 'High']) }}"
                        class="px-3 py-2 rounded-lg border border-[#D7DDE5] bg-white text-[#E74C3C]">
                        High Urgency
                    </a>
                    <a href="{{ route('technician.my_jobs', ['status' => 'In_Progress']) }}"
                        class="px-3 py-2 rounded-lg border border-[#D7DDE5] bg-white text-[#3498DB]">
                        In Progress
                    </a>
                    <a href="{{ route('technician.my_jobs', ['status' => 'Overdue']) }}"
                        class="px-3 py-2 rounded-lg border border-[#D7DDE5] bg-white text-[#F39C12]">
                        Overdue
                    </a>
                </div>
            </div>

            {{-- Stats cards --}}
            <div class="grid gap-4 sm:grid-cols-4 mt-6">
                @foreach ($stats as $stat)
                <div class="rounded-xl p-4 text-white {{ $stat['bg_class'] }}">
                        <div class="text-sm" style="color:rgba(255,255,255,0.8);">
                            {{ $stat['label'] }}
                        </div>
                        <div class="text-3xl font-semibold mt-1">
                            {{ $stat['value'] }}
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Overdue banner --}}
            <div class="mt-6 rounded-xl border border-[#F39C12] bg-[#FCEED3] px-4 py-3 text-[#7F3C0A]">
                <div class="font-semibold text-[#7F3C0A]">Overdue tasks</div>
                <p class="text-sm text-[#7F3C0A]">
                    You have {{ $overdueCount }} overdue task{{ $overdueCount === 1 ? '' : 's' }}.
                    Make sure to focus on these things first to meet the agreed-upon time or quality standards.
                </p>
                <div class="flex flex-wrap gap-2 mt-2 text-sm">
                    <a href="{{ route('technician.my_jobs', ['status' => 'Overdue']) }}"
                        class="px-3 py-1 rounded-full bg-[#F39C12] text-white">
                        View overdue
                    </a>

                    @if($nextOverdue)
                        <a href="{{ route('technician.job_details', $nextOverdue->id) }}"
                            class="px-3 py-1 rounded-full bg-[#1F4E79] text-white">
                            Next task ->
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Placeholder for chart / analytics --}}
            <div class="lg:col-span-2 rounded-2xl shadow-sm border border-[#D7DDE5] bg-white p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-[#2C3E50]">Tasks by status</h2>
                    <a href="{{ route('technician.my_jobs') }}" class="text-sm font-semibold text-[#1F4E79]">
                        View tasks
                    </a>
                </div>
                <div
                    class="h-56 bg-slate-100 rounded-xl flex items-center justify-center border border-dashed border-[#D7DDE5]">
                    <span class="text-sm text-[#7F8C8D]">
                        Chart placeholder (status / category) – can be powered by Chart.js later
                    </span>
                </div>
            </div>

            {{-- Recent activity --}}
            <div class="rounded-2xl shadow-sm border border-[#D7DDE5] bg-white p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-[#2C3E50]">Recent activity</h2>
                </div>
                <ul class="space-y-3 text-sm text-[#2C3E50]">
                    @forelse ($recent as $item)
                        <li class="rounded-lg px-3 py-2 bg-[#F5F7FA]">
                            {{ $item }}
                        </li>
                    @empty
                        <li class="text-sm text-[#7F8C8D]">
                            No recent activity yet.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </section>
@endsection
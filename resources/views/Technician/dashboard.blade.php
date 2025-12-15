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
        <div class="rounded-2xl border border-transparent bg-gradient-to-r from-[#102A43] via-[#1F4E79] to-[#2A7ABF] text-white p-6 shadow-lg">
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

        <div class="grid gap-4 sm:grid-cols-4">
            @foreach ($stats as $stat)
                <div class="rounded-xl p-4 text-white shadow-md {{ $stat['bg_class'] }}">
                    <div class="text-sm" style="color:rgba(255,255,255,0.8);">
                        {{ $stat['label'] }}
                    </div>
                    <div class="text-3xl font-semibold mt-1">
                        {{ $stat['value'] }}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="rounded-2xl border border-[#F39C12] bg-[#FCEED3] px-5 py-4 shadow-sm">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="font-semibold text-[#7F3C0A] text-lg">Overdue tasks</div>
                    <p class="text-sm text-[#7F3C0A]">
                        You have {{ $overdueCount }} overdue task{{ $overdueCount === 1 ? '' : 's' }}. Focus on these first.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2 text-sm">
                    <a href="{{ route('technician.tasks', ['status' => 'Overdue']) }}"
                        class="px-3 py-2 rounded-full bg-[#F39C12] text-white font-semibold shadow-sm">
                        View overdue
                    </a>

                    @if($nextOverdue)
                        <a href="{{ route('technician.task_detail', $nextOverdue->id) }}"
                            class="px-3 py-2 rounded-full bg-[#1F4E79] text-white font-semibold shadow-sm">
                            Next task →
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
                    <a href="{{ route('technician.tasks') }}" class="text-sm font-semibold text-[#1F4E79]">
                        View tasks
                    </a>
                </div>
                <div
                    class="h-56 bg-gradient-to-br from-[#F5F7FA] to-[#E8EEF7] rounded-xl flex items-center justify-center border border-dashed border-[#D7DDE5]">
                    <span class="text-sm text-[#7F8C8D]">
                        Chart placeholder (status / category)
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
                            <span class="whitespace-pre-line">{{ $item }}</span>
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

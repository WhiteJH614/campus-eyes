@extends('layouts.app')

@php
    $pageTitle = 'Assigned Jobs';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'Technician Dashboard', 'url' => route('technician.dashboard')],
        ['label' => 'Assigned Jobs'],
    ];

    $urgColors = [
        'high' => ['#E74C3C', '#FFFFFF'],
        'medium' => ['#F1C40F', '#2C3E50'],
        'low' => ['#2ECC71', '#FFFFFF'],
    ];

    $statusColors = [
        'Pending' => ['#95A5A6', '#FFFFFF'],
        'Assigned' => ['#F39C12', '#FFFFFF'],
        'In_Progress' => ['#3498DB', '#FFFFFF'],
        'Completed' => ['#27AE60', '#FFFFFF'],
    ];
    $nowMy = now('Asia/Kuala_Lumpur');
@endphp

@section('content')
    <section class="space-y-6">
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
            <form class="mt-4 grid gap-3 lg:grid-cols-5" method="get" action="{{ route('technician.tasks') }}">
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search by Report ID"
                    class="rounded-lg px-3 py-2 border border-[#D7DDE5] bg-white text-[#2C3E50]" />

                <select name="status" class="rounded-lg px-3 py-2 border border-[#D7DDE5] bg-white text-[#2C3E50]">
                    <option value="">Status</option>
                    @foreach (['Pending', 'Assigned', 'In_Progress', 'Overdue'] as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>
                            {{ str_replace('_', ' ', $status) }}
                        </option>
                    @endforeach
                </select>

                <select name="urgency" class="rounded-lg px-3 py-2 border border-[#D7DDE5] bg-white text-[#2C3E50]">
                    <option value="">Urgency</option>
                    @foreach (['High', 'Medium', 'Low'] as $u)
                        <option value="{{ $u }}" @selected(($filters['urgency'] ?? '') === $u)>{{ $u }}</option>
                    @endforeach
                </select>

                <select name="sort" class="rounded-lg px-3 py-2 border border-[#D7DDE5] bg-white text-[#2C3E50]">
                    <option value="due" @selected(($filters['sort'] ?? '') === 'due')>Sort: Due date (default)</option>
                    <option value="urgency" @selected(($filters['sort'] ?? '') === 'urgency')>Sort: Urgency</option>
                    <option value="block" @selected(($filters['sort'] ?? '') === 'block')>Sort: Block</option>
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
                        @forelse ($jobs as $job)
                            @php
                                $urgKey = strtolower($job->urgency ?? 'low');
                                $urgBg = $urgColors[$urgKey][0] ?? '#D7DDE5';
                                $urgFg = $urgColors[$urgKey][1] ?? '#2C3E50';

                                $statusBg = $statusColors[$job->status][0] ?? '#D7DDE5';
                                $statusFg = $statusColors[$job->status][1] ?? '#2C3E50';
                                $due = $job->due_at;
                                $isOverdue = ($job->status === 'Overdue') || ($due && $due->isPast() && $job->status !== 'Completed');
                                $overdueDuration = $isOverdue && $due ? $due->copy()->setTimezone($nowMy->timezone)->diffForHumans($nowMy, true) : '-';
                            @endphp
                            <tr class="border-t border-[#D7DDE5] hover:bg-[#F9FBFF]">
                                <td class="px-3 py-2 font-semibold text-[#1F4E79]">
                                    <a href="/technician/tasks/{{ $job->id }}">
                                        {{ $job->id }}
                                    </a>
                                </td>
                                <td class="px-3 py-2">
                                    {{ $job->created_at?->format('d M Y H:i') }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ optional($job->room->block ?? null)->block_name ?? '' }},
                                    {{ optional($job->room)->room_name ?? '' }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ optional($job->category)->name ?? '-' }}
                                </td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold inline-block w-full text-center"
                                        style="background-color: {{ $urgBg }}; color: {{ $urgFg }};">
                                        {{ $job->urgency ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold inline-block w-full text-center"
                                        style="background-color: {{ $statusBg }}; color: {{ $statusFg }};">
                                        {{ str_replace('_', ' ', $job->status) }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    @if ($isOverdue)
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold inline-block w-full text-center"
                                            style="background-color:#E74C3C; color:#FFFFFF;">
                                            Overdue {{ $overdueDuration }}
                                        </span>
                                    @else
                                        <span class="text-xs text-[#7F8C8D]">On track</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    {{ $job->due_at?->format('d M Y H:i') ?? '-' }}
                                </td>
                                <td class="px-3 py-2">
                                    <a href="/technician/tasks/{{ $job->id }}"
                                        class="text-xs font-semibold text-[#1F4E79]">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-4 text-center text-[#7F8C8D]">
                                    No assigned jobs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $jobs->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection

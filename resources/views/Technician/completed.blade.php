@extends('layouts.app')

@php
    $pageTitle = 'Completed Tasks';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'Technician Dashboard', 'url' => route('technician.dashboard')],
        ['label' => 'Completed Tasks'],
    ];
    $summary = $summary ?? ['total' => 0, 'avg_time' => '-', 'high_urgency' => 0];
    $rows = $rows ?? [];
    $pagination = $pagination ?? null;
    $urgColors = [
        'high' => ['#E74C3C', '#FFFFFF'],
        'medium' => ['#F1C40F', '#2C3E50'],
        'low' => ['#2ECC71', '#FFFFFF'],
    ];
@endphp

@section('content')
    <section class="space-y-6">
        <div class="rounded-2xl shadow-sm border border-[#D7DDE5] bg-white p-6 space-y-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-[#2C3E50]">Completed Tasks</h1>
                    <p class="text-sm text-[#7F8C8D]">All tasks you've closed, with filters and history.</p>
                </div>
                <div class="flex flex-wrap gap-2 text-sm">
                    <a href="?range=7" class="px-3 py-2 rounded-lg border border-[#D7DDE5] bg-white text-[#1F4E79]">Last 7 days</a>
                    <a href="?range=30" class="px-3 py-2 rounded-lg border border-[#D7DDE5] bg-white text-[#1F4E79]">Last 30 days</a>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-transparent bg-gradient-to-r from-[#1F4E79] to-[#3498DB] p-5 text-white shadow">
                    <div class="text-sm opacity-80">Completed tasks</div>
                    <div class="text-3xl font-semibold mt-1">{{ $summary['total'] }}</div>
                    <p class="text-xs opacity-80 mt-2">Total jobs closed in the selected window.</p>
                </div>
                <div class="rounded-2xl border border-[#D7DDE5] bg-[#F5F7FA] p-5 shadow-sm">
                    <div class="text-sm text-[#7F8C8D]">Avg resolution time</div>
                    <div class="text-2xl font-semibold text-[#2C3E50]">{{ $summary['avg_time'] }}</div>
                </div>
                <div class="rounded-2xl border border-[#D7DDE5] bg-[#F5F7FA] p-5 shadow-sm">
                    <div class="text-sm text-[#7F8C8D]">High urgency completed</div>
                    <div class="text-2xl font-semibold text-[#2C3E50]">{{ $summary['high_urgency'] }}</div>
                </div>
            </div>

            <form class="grid gap-3 lg:grid-cols-5 items-center rounded-xl bg-[#F8FBFF] border border-[#D7DDE5] p-4 shadow-sm" method="get">
                <input type="date" name="from" class="rounded-lg px-3 py-2 border border-[#D7DDE5] bg-white text-[#2C3E50]" />
                <input type="date" name="to" class="rounded-lg px-3 py-2 border border-[#D7DDE5] bg-white text-[#2C3E50]" />
                <select name="category" class="rounded-lg px-3 py-2 border border-[#D7DDE5] bg-white text-[#2C3E50]">
                    <option value="">Category</option>
                    <option>Electrical</option>
                    <option>IT</option>
                    <option>HVAC</option>
                </select>
                <select name="block" class="rounded-lg px-3 py-2 border border-[#D7DDE5] bg-white text-[#2C3E50]">
                    <option value="">Block</option>
                    <option>Block A</option>
                    <option>Block B</option>
                    <option>Block C</option>
                    <option>Block M</option>
                </select>
                <div class="grid sm:grid-cols-2 lg:grid-cols-1 gap-3">
                    <input type="text" name="q" placeholder="Search notes (lamp, projector)" class="rounded-lg px-3 py-2 border border-[#D7DDE5] bg-white text-[#2C3E50]" />
                    <button type="submit" class="rounded-lg px-4 py-2 font-semibold bg-[#1F4E79] text-white shadow-sm">Apply</button>
                </div>
            </form>

            <div id="history" class="overflow-x-auto mt-2">
                <table class="min-w-full text-sm rounded-xl overflow-hidden border border-[#D7DDE5]">
                    <thead>
                        <tr class="bg-[#F5F7FA] text-[#2C3E50]">
                            <th class="text-left px-3 py-2">Report ID</th>
                            <th class="text-left px-3 py-2">Location</th>
                            <th class="text-left px-3 py-2">Category</th>
                            <th class="text-left px-3 py-2">Urgency</th>
                            <th class="text-left px-3 py-2">Original Due Date</th>
                            <th class="text-left px-3 py-2">Completed</th>
                            <th class="text-left px-3 py-2">Status</th>
                            <th class="text-left px-3 py-2">Duration</th>
                            <th class="text-left px-3 py-2">Notes</th>
                            <th class="text-left px-3 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-[#2C3E50] divide-y divide-[#D7DDE5]">
                        @forelse ($rows as $row)
                            @php
                                $u = strtolower($row['urg']);
                                $urgBg = $urgColors[$u][0] ?? '#D7DDE5';
                                $urgFg = $urgColors[$u][1] ?? '#2C3E50';
                            @endphp
                            <tr class="bg-white">
                                <td class="px-3 py-2 font-semibold text-[#1F4E79]">{{ $row['id'] }}</td>
                                <td class="px-3 py-2">{{ $row['loc'] }}</td>
                                <td class="px-3 py-2">{{ $row['cat'] }}</td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold" style="background:{{ $urgBg }};color:{{ $urgFg }};">
                                        {{ $row['urg'] }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">{{ $row['due_at'] }}</td>
                                <td class="px-3 py-2">{{ $row['done'] }}</td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold" style="background:#27AE60;color:#FFFFFF;">
                                        {{ str_replace('_', ' ', $row['status'] ?? 'Completed') }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">{{ $row['duration'] }}</td>
                                <td class="px-3 py-2 text-[#7F8C8D]">{{ $row['notes'] }}</td>
                                <td class="px-3 py-2">
                                    <a href="{{ route('technician.task_detail', $row['report_id']) }}"
                                        class="text-sm font-semibold text-[#1F4E79] underline">
                                        View details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-3 py-4 text-center text-[#7F8C8D]">No completed tasks found for the selected filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($pagination && $pagination->hasPages())
                <div class="mt-4">
                    {{ $pagination->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection

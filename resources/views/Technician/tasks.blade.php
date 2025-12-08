@extends('layouts.app')

@php
    $pageTitle = 'Assigned Tasks';
    $user = $user ?? (session('user') ?? ['name' => 'Technician', 'role' => 'technician']);
    $breadcrumbs = [
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'Assigned Tasks'],
    ];
    $rows = [
        ['id' => 'R-210', 'loc' => 'Block A, Lab 2', 'cat' => 'Electrical', 'urg' => 'High', 'status' => 'In Progress', 'date' => '2025-12-02', 'due' => '2025-12-04'],
        ['id' => 'R-212', 'loc' => 'Library, Floor 3', 'cat' => 'IT', 'urg' => 'Medium', 'status' => 'Assigned', 'date' => '2025-12-02', 'due' => '2025-12-05'],
        ['id' => 'R-208', 'loc' => 'Block C, Room 12', 'cat' => 'HVAC', 'urg' => 'Low', 'status' => 'Assigned', 'date' => '2025-12-01', 'due' => '2025-12-06'],
    ];
    $urgColors = [
        'high' => ['#E74C3C', '#FFFFFF'],
        'medium' => ['#F1C40F', '#2C3E50'],
        'low' => ['#2ECC71', '#FFFFFF'],
    ];
    $statusColors = [
        'assigned' => ['#F39C12', '#FFFFFF'],
        'in progress' => ['#3498DB', '#FFFFFF'],
        'completed' => ['#27AE60', '#FFFFFF'],
        'escalated' => ['#E74C3C', '#FFFFFF'],
    ];
@endphp

@section('content')
    <section class="space-y-6">
        <div class="rounded-2xl shadow-sm border p-6" style="background:#FFFFFF;border-color:#D7DDE5;">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold" style="color:#2C3E50;">Assigned Tasks</h1>
                    <p class="text-sm" style="color:#7F8C8D;">Tasks assigned to you. Filter, search, export, and act fast.</p>
                </div>
                <div class="flex flex-wrap gap-2 text-sm">
                    <a href="/tech/tasks/export" class="px-3 py-2 rounded-lg border" style="background:#FFFFFF;border-color:#D7DDE5;color:#1F4E79;">Export My Tasks (CSV)</a>
                </div>
            </div>
            <form class="mt-4 grid gap-3 lg:grid-cols-4" method="get">
                <input type="text" name="q" placeholder="Search (aircond, projector, pipe)" class="rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
                <select name="status" class="rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;">
                    <option value="">Status</option>
                    <option>Assigned</option>
                    <option>In Progress</option>
                    <option>Completed</option>
                    <option>Escalated</option>
                </select>
                <select name="urgency" class="rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;">
                    <option value="">Urgency</option>
                    <option>High</option>
                    <option>Medium</option>
                    <option>Low</option>
                </select>
                <select name="block" class="rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;">
                    <option value="">Block</option>
                    <option>Block A</option>
                    <option>Block B</option>
                    <option>Block C</option>
                    <option>Block M</option>
                </select>
                <select name="sort" class="rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;">
                    <option value="assigned_desc">Sort: Assigned Date</option>
                    <option value="urgency">Sort: Urgency</option>
                    <option value="due">Sort: Due Date</option>
                </select>
                <button type="submit" class="rounded-lg px-4 py-2 font-semibold" style="background:#1F4E79;color:#FFFFFF;">Apply</button>
            </form>
            <div class="overflow-x-auto mt-4">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr style="background:#F5F7FA;color:#2C3E50;">
                            <th class="text-left px-3 py-2">Ticket ID</th>
                            <th class="text-left px-3 py-2">Location</th>
                            <th class="text-left px-3 py-2">Category</th>
                            <th class="text-left px-3 py-2">Urgency</th>
                            <th class="text-left px-3 py-2">Status</th>
                            <th class="text-left px-3 py-2">Assigned Date</th>
                            <th class="text-left px-3 py-2">Due Date</th>
                            <th class="text-left px-3 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="color:#2C3E50;">
                        @foreach ($rows as $row)
                            @php
                                $u = strtolower($row['urg']);
                                $urgBg = $urgColors[$u][0] ?? '#D7DDE5';
                                $urgFg = $urgColors[$u][1] ?? '#2C3E50';
                                $s = strtolower($row['status']);
                                $statusBg = $statusColors[$s][0] ?? '#D7DDE5';
                                $statusFg = $statusColors[$s][1] ?? '#2C3E50';
                            @endphp
                            <tr class="border-t" style="border-color:#D7DDE5;">
                                <td class="px-3 py-2 font-semibold" style="color:#1F4E79;">{{ $row['id'] }}</td>
                                <td class="px-3 py-2">{{ $row['loc'] }}</td>
                                <td class="px-3 py-2">{{ $row['cat'] }}</td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold" style="background:{{ $urgBg }};color:{{ $urgFg }};">
                                        {{ $row['urg'] }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold" style="background:{{ $statusBg }};color:{{ $statusFg }};">
                                        {{ $row['status'] }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">{{ $row['date'] }}</td>
                                <td class="px-3 py-2">{{ $row['due'] }}</td>
                                <td class="px-3 py-2 flex flex-wrap gap-2">
                                    <a href="/tech/tasks/{{ urlencode($row['id']) }}" class="text-sm font-semibold" style="color:#1F4E79;">Details</a>
                                    <a href="/tech/tasks/{{ urlencode($row['id']) }}/start" class="text-sm font-semibold" style="color:#3498DB;">Start Work</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

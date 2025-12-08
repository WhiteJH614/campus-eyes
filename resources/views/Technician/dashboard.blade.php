@extends('layouts.app')

@php
    $pageTitle = 'Technician Dashboard';
    $user = $user ?? (session('user') ?? ['name' => 'Technician', 'role' => 'technician']);
    $breadcrumbs = [
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'Technician Dashboard'],
    ];
    $stats = [
        ['label' => 'Assigned', 'value' => 8, 'bg' => '#1F4E79'],
        ['label' => 'In Progress', 'value' => 5, 'bg' => '#3498DB'],
        ['label' => 'Completed (month)', 'value' => 18, 'bg' => '#27AE60'],
        ['label' => 'Overdue', 'value' => 2, 'bg' => '#F39C12'],
    ];
    $recent = [
        'Task R-215 marked as Completed',
        'Task R-214 status changed to In Progress',
        'Task R-210 new note added',
        'Task R-208 assigned to you',
        'Task R-199 marked as Completed',
    ];
@endphp

@section('content')
    <section class="space-y-8">
        <div class="rounded-2xl shadow-sm border p-6" style="background:#FFFFFF;border-color:#D7DDE5;">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold" style="color:#2C3E50;">Technician Dashboard</h1>
                    <p class="text-sm" style="color:#7F8C8D;">Monitor your workload, overdue items, and recent updates.</p>
                </div>
                <div class="flex flex-wrap gap-2 text-sm">
                    <a href="/tech/tasks" class="px-3 py-2 rounded-lg border" style="background:#FFFFFF;border-color:#D7DDE5;color:#1F4E79;">All Tasks</a>
                    <a href="/tech/tasks?urgency=high" class="px-3 py-2 rounded-lg border" style="background:#FFFFFF;border-color:#D7DDE5;color:#E74C3C;">High Urgency</a>
                    <a href="/tech/tasks?status=in-progress" class="px-3 py-2 rounded-lg border" style="background:#FFFFFF;border-color:#D7DDE5;color:#3498DB;">In Progress</a>
                    <a href="/tech/tasks?status=overdue" class="px-3 py-2 rounded-lg border" style="background:#FFFFFF;border-color:#D7DDE5;color:#F39C12;">Overdue</a>
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-4 mt-6">
                @foreach ($stats as $stat)
                    <div class="rounded-xl p-4 text-white" style="background:{{ $stat['bg'] }};">
                        <div class="text-sm" style="color:rgba(255,255,255,0.8);">{{ $stat['label'] }}</div>
                        <div class="text-3xl font-semibold mt-1">{{ $stat['value'] }}</div>
                    </div>
                @endforeach
            </div>
            <div class="mt-6 rounded-xl border px-4 py-3" style="border-color:#F39C12;background:#FCEED3;color:#7F3C0A;">
                <div class="font-semibold">Overdue tasks</div>
                <p class="text-sm">You have 2 overdue tasks. Prioritize these to maintain SLA.</p>
                <div class="flex flex-wrap gap-2 mt-2 text-sm">
                    <a href="/tech/tasks?status=overdue" class="px-3 py-1 rounded-full" style="background:#F39C12;color:#FFFFFF;">View overdue</a>
                    <a href="/tech/tasks/R-190" class="px-3 py-1 rounded-full" style="background:#1F4E79;color:#FFFFFF;">Next task -></a>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 rounded-2xl shadow-sm border p-6" style="background:#FFFFFF;border-color:#D7DDE5;">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold" style="color:#2C3E50;">Tasks by status</h2>
                    <a href="/tech/tasks" class="text-sm font-semibold" style="color:#1F4E79;">View tasks</a>
                </div>
                <div class="h-56 bg-slate-100 rounded-xl flex items-center justify-center" style="border:1px dashed #D7DDE5;">
                    <span class="text-sm" style="color:#7F8C8D;">Chart placeholder (status/category)</span>
                </div>
            </div>
            <div class="rounded-2xl shadow-sm border p-6" style="background:#FFFFFF;border-color:#D7DDE5;">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold" style="color:#2C3E50;">Recent activity</h2>
                </div>
                <ul class="space-y-3 text-sm" style="color:#2C3E50;">
                    @foreach ($recent as $item)
                        <li class="rounded-lg px-3 py-2" style="background:#F5F7FA;">
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>
@endsection

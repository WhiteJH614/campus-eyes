{{-- Author: Tan Jun Yan --}}
@extends('layouts.app')

@php
    $pageTitle = 'Reporter Dashboard';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'Reporter Dashboard'],
    ];

    // Get reports data
    $reports = Auth::user()->reports()->latest()->take(5)->get();
@endphp

@section('content')
    <div class="py-8 space-y-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Personalized Hero Section -->
            <div class="relative overflow-hidden rounded-2xl text-white shadow-lg mb-8"
                style="background:linear-gradient(120deg,#1F4E79,#285F96);">
                <div class="absolute inset-0" style="background:linear-gradient(180deg,rgba(255,255,255,0.08),transparent);"></div>
                <div class="relative px-8 py-10 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div>
                        <h1 class="text-3xl font-semibold">Welcome back, {{ Auth::user()->name }}!</h1>
                        <p class="mt-2 text-lg" style="color:rgba(255,255,255,0.9);">
                            Member since {{ Auth::user()->created_at->format('F Y') }} • {{ Auth::user()->reporter_role ?? 'Reporter' }}
                        </p>
                    </div>
                    <!-- Stats in Hero -->
                    @if(Auth::user()->role === 'Reporter')
                    <div class="flex gap-4">
                        <div class="rounded-xl px-5 py-3 border backdrop-blur-sm"
                            style="background:rgba(255,255,255,0.12);border-color:rgba(255,255,255,0.25);">
                            <div class="text-xs uppercase tracking-wider" style="color:rgba(255,255,255,0.8);">Pending</div>
                            <div class="text-2xl font-bold">{{ Auth::user()->reports()->where('status', 'Pending')->count() }}</div>
                        </div>
                        <div class="rounded-xl px-5 py-3 border backdrop-blur-sm"
                            style="background:rgba(255,255,255,0.12);border-color:rgba(255,255,255,0.25);">
                            <div class="text-xs uppercase tracking-wider" style="color:rgba(255,255,255,0.8);">In Progress</div>
                            <div class="text-2xl font-bold">{{ Auth::user()->reports()->whereIn('status', ['Assigned', 'In_Progress'])->count() }}</div>
                        </div>
                        <div class="rounded-xl px-5 py-3 border backdrop-blur-sm"
                            style="background:rgba(255,255,255,0.12);border-color:rgba(255,255,255,0.25);">
                            <div class="text-xs uppercase tracking-wider" style="color:rgba(255,255,255,0.8);">Completed</div>
                            <div class="text-2xl font-bold">{{ Auth::user()->reports()->where('status', 'Completed')->count() }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if(Auth::user()->role === 'Reporter')
            <!-- Action Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Submit Report -->
                <a href="{{ route('reports.create') }}" 
                   class="group relative rounded-xl border p-6 shadow-sm hover:shadow-md transition-all duration-200"
                   style="background:#FFFFFF;border-color:#D7DDE5;">
                    <div class="mb-4 inline-flex items-center justify-center w-12 h-12 rounded-lg bg-blue-50 text-2xl">
                        📝
                    </div>
                    <h3 class="text-lg font-semibold mb-1" style="color:#1F4E79;">Submit New Report</h3>
                    <p class="text-sm" style="color:#7F8C8D;">
                        Found an issue? Submit a new maintenance request in seconds.
                    </p>
                    <div class="mt-4 flex items-center text-sm font-medium" style="color:#1ABC9C;">
                        Get Started <span class="ml-1 transition-transform group-hover:translate-x-1">→</span>
                    </div>
                </a>

                <!-- My Reports -->
                <a href="{{ route('reports.index') }}" 
                   class="group relative rounded-xl border p-6 shadow-sm hover:shadow-md transition-all duration-200"
                   style="background:#FFFFFF;border-color:#D7DDE5;">
                    <div class="mb-4 inline-flex items-center justify-center w-12 h-12 rounded-lg bg-blue-50 text-2xl">
                        📋
                    </div>
                    <h3 class="text-lg font-semibold mb-1" style="color:#1F4E79;">My Reports</h3>
                    <p class="text-sm" style="color:#7F8C8D;">
                        Track the status of your submitted issues and view history.
                    </p>
                    <div class="mt-4 flex items-center text-sm font-medium" style="color:#3498DB;">
                        View All <span class="ml-1 transition-transform group-hover:translate-x-1">→</span>
                    </div>
                </a>

                <!-- My Profile -->
                <a href="{{ route('profile.edit') }}" 
                   class="group relative rounded-xl border p-6 shadow-sm hover:shadow-md transition-all duration-200"
                   style="background:#FFFFFF;border-color:#D7DDE5;">
                    <div class="mb-4 inline-flex items-center justify-center w-12 h-12 rounded-lg bg-blue-50 text-2xl">
                        👤
                    </div>
                    <h3 class="text-lg font-semibold mb-1" style="color:#1F4E79;">My Profile</h3>
                    <p class="text-sm" style="color:#7F8C8D;">
                        Update your personal information and preferences.
                    </p>
                    <div class="mt-4 flex items-center text-sm font-medium" style="color:#2C3E50;">
                        Manage <span class="ml-1 transition-transform group-hover:translate-x-1">→</span>
                    </div>
                </a>
            </div>

            <!-- Recent Reports Section -->
            <div class="rounded-2xl border shadow-sm bg-white" style="border-color:#D7DDE5;">
                <div class="p-6 border-b" style="border-color:#D7DDE5;">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold" style="color:#2C3E50;">Recent Activity</h3>
                        <a href="{{ route('reports.index') }}" class="text-sm font-medium hover:underline" style="color:#3498DB;">View Full History</a>
                    </div>
                </div>
                
                @if($reports->isEmpty())
                    <div class="p-12 text-center">
                        <div class="text-gray-400 mb-3 text-4xl">📭</div>
                        <h4 class="text-lg font-medium text-gray-900">No reports filed yet</h4>
                        <p class="text-gray-500 mt-1">When you spot an issue, submit a report to get it fixed.</p>
                        <a href="{{ route('reports.create') }}" class="inline-block mt-4 px-4 py-2 rounded-lg text-sm font-semibold" 
                           style="background:#1F4E79;color:#FFFFFF;">
                            Submit First Report
                        </a>
                    </div>
                @else
                    <div class="divide-y" style="border-color:#eaeff5;">
                        @foreach($reports as $report)
                            <div class="p-4 hover:bg-gray-50 transition-colors flex items-center justify-between">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold"
                                        style="background:{{ $report->status === 'Completed' ? '#D1F2EB' : '#F5F7FA' }};
                                               color:{{ $report->status === 'Completed' ? '#1ABC9C' : '#7F8C8D' }};">
                                        #{{ $report->id }}
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-900">
                                            {{ Str::limit($report->description, 60) }}
                                        </h4>
                                        <div class="flex items-center gap-2 mt-1 text-xs text-gray-500">
                                            <span>📅 {{ $report->created_at->diffForHumans() }}</span>
                                            <span>•</span>
                                            <span>{{ $report->room->block->block_name }} - {{ $report->room->room_name }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border"
                                        style="
                                        @if($report->status === 'Completed') background:#D1F2EB;color:#1ABC9C;border-color:transparent
                                        @elseif($report->status === 'In_Progress') background:#EBF5FB;color:#3498DB;border-color:transparent
                                        @else background:#F5F7FA;color:#7F8C8D;border-color:#D7DDE5 @endif
                                        ">
                                        {{ str_replace('_', ' ', $report->status) }}
                                    </span>
                                    <a href="{{ route('reports.show', $report) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors">
                                        →
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            @endif
        </div>
    </div>
@endsection
@extends('layouts.app')

@php
    $pageTitle = 'Reporter Dashboard';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'Reporter Dashboard'],
    ];
@endphp

@section('content')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-2">Welcome, {{ Auth::user()->name }}!</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        You are logged in as a <strong>{{ Auth::user()->reporter_role ?? 'Reporter' }}</strong>.
                    </p>
                </div>
            </div>

            @if(Auth::user()->role === 'Reporter')
            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <a href="{{ route('reports.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg p-6 transition-colors">
                    <div class="text-3xl mb-2">📝</div>
                    <h4 class="font-semibold">Submit New Report</h4>
                    <p class="text-indigo-200 text-sm">Report a maintenance issue</p>
                </a>
                <a href="{{ route('reports.index') }}" class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg p-6 shadow-sm border dark:border-gray-700 transition-colors">
                    <div class="text-3xl mb-2">📋</div>
                    <h4 class="font-semibold text-gray-900 dark:text-white">My Reports</h4>
                    <p class="text-gray-500 text-sm">View all your submitted reports</p>
                </a>
                <a href="{{ route('profile.edit') }}" class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg p-6 shadow-sm border dark:border-gray-700 transition-colors">
                    <div class="text-3xl mb-2">👤</div>
                    <h4 class="font-semibold text-gray-900 dark:text-white">My Profile</h4>
                    <p class="text-gray-500 text-sm">Update your information</p>
                </a>
            </div>

            <!-- Reports Summary -->
            @php
                $reports = Auth::user()->reports()->latest()->take(5)->get();
                $pendingCount = Auth::user()->reports()->where('status', 'Pending')->count();
                $inProgressCount = Auth::user()->reports()->whereIn('status', ['Assigned', 'In_Progress'])->count();
                $completedCount = Auth::user()->reports()->where('status', 'Completed')->count();
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-6 border border-yellow-200 dark:border-yellow-800">
                    <div class="text-3xl font-bold text-yellow-600">{{ $pendingCount }}</div>
                    <div class="text-yellow-700 dark:text-yellow-400">Pending Reports</div>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 border border-blue-200 dark:border-blue-800">
                    <div class="text-3xl font-bold text-blue-600">{{ $inProgressCount }}</div>
                    <div class="text-blue-700 dark:text-blue-400">In Progress</div>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-6 border border-green-200 dark:border-green-800">
                    <div class="text-3xl font-bold text-green-600">{{ $completedCount }}</div>
                    <div class="text-green-700 dark:text-green-400">Completed</div>
                </div>
            </div>

            <!-- Recent Reports -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Recent Reports</h3>
                        <a href="{{ route('reports.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">View All →</a>
                    </div>

                    @if($reports->isEmpty())
                        <p class="text-gray-500 text-center py-8">No reports yet. <a href="{{ route('reports.create') }}" class="text-indigo-600 hover:underline">Submit your first report</a></p>
                    @else
                        <div class="space-y-3">
                            @foreach($reports as $report)
                                <a href="{{ route('reports.show', $report) }}" class="block p-4 border dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <span class="font-medium">#{{ $report->id }}</span>
                                            <span class="text-gray-600 dark:text-gray-400">{{ Str::limit($report->description, 50) }}</span>
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($report->status === 'Completed') bg-green-100 text-green-800
                                            @elseif($report->status === 'In_Progress') bg-blue-100 text-blue-800
                                            @elseif($report->status === 'Assigned') bg-purple-100 text-purple-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ str_replace('_', ' ', $report->status) }}
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-500 mt-1">{{ $report->created_at->diffForHumans() }}</div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

@endsection
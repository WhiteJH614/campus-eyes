@extends('layouts.app')

@php
    $pageTitle = 'My Reports';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'My Reports'],
    ];
@endphp

@section('content')
    <div class="py-8 space-y-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Radiant Header -->
            <div class="relative overflow-hidden rounded-2xl text-white shadow-lg mb-6"
                style="background:linear-gradient(120deg,#1F4E79,#285F96);">
                <div class="absolute inset-0" style="background:linear-gradient(180deg,rgba(255,255,255,0.08),transparent);"></div>
                <div class="relative px-8 py-8 md:flex md:items-center md:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold">My Reports</h2>
                        <p class="mt-1 opacity-90">Track and manage your submitted maintenance requests.</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <a href="{{ route('reports.create') }}" 
                           class="inline-flex items-center gap-2 px-5 py-2.5 font-semibold text-sm rounded-lg shadow-md hover:bg-gray-50 transition-colors"
                           style="background:#FFFFFF;color:#1F4E79;">
                            <span class="text-lg">+</span> Create New Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Status Filters -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 sm:gap-4 mb-6">
                <!-- All Reports -->
                <a href="{{ route('reports.index') }}" 
                   class="flex flex-col items-center justify-center p-3 sm:p-4 rounded-xl border transition-all duration-200 hover:shadow-md
                   {{ !request('status') ? 'bg-white border-indigo-600 ring-1 ring-indigo-600 shadow-sm' : 'bg-white border-gray-200 hover:border-indigo-300' }}">
                    <span class="text-xl sm:text-2xl font-bold" style="color:#2C3E50;">
                        {{ $statusCounts->sum() }}
                    </span>
                    <span class="text-xs font-bold uppercase tracking-wider mt-1" style="color:#7F8C8D;">All</span>
                </a>

                @foreach(['Pending', 'Assigned', 'In_Progress', 'Completed'] as $status)
                    @php
                       $count = $statusCounts->get($status, 0);
                       $isActive = request('status') === $status;
                       $colorCode = match($status) {
                           'Pending' => '#F39C12',     // Orange/Yellow
                           'Assigned' => '#9B59B6',    // Purple
                           'In_Progress' => '#3498DB', // Blue
                           'Completed' => '#1ABC9C',   // Teal/Green
                           default => '#95A5A6'
                       };
                       $label = str_replace('_', ' ', $status);
                    @endphp
                    <a href="{{ route('reports.index', ['status' => $status]) }}" 
                       class="flex flex-col items-center justify-center p-3 sm:p-4 rounded-xl border transition-all duration-200 hover:shadow-md
                       {{ $isActive ? 'bg-white ring-1 shadow-sm' : 'bg-white hover:border-gray-300' }}"
                       style="{{ $isActive ? 'border-color:' . $colorCode . '; ring-color:' . $colorCode : 'border-color:#D7DDE5' }}">
                        <span class="text-xl sm:text-2xl font-bold" style="color:{{ $colorCode }};">
                            {{ $count }}
                        </span>
                        <span class="text-xs font-bold uppercase tracking-wider mt-1" style="color:#7F8C8D;">{{ $label }}</span>
                    </a>
                @endforeach
            </div>

            @if (session('success'))
                <div class="rounded-xl border p-4 mb-6 flex items-start gap-3"
                    style="background:#D1F2EB;border-color:#A3E4D7;color:#0E6251;">
                    <div class="text-xl">✅</div>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            <div class="rounded-2xl border shadow-sm bg-white overflow-hidden" style="border-color:#D7DDE5;">
                @if ($reports->isEmpty())
                    <div class="p-16 text-center">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full mb-4" 
                             style="background:#F5F7FA;">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium" style="color:#2C3E50;">No reports found</h3>
                        <p class="text-gray-500 mt-2 max-w-sm mx-auto">You haven't submitted any reports yet. Once you do, they will appear here down to the last detail.</p>
                        <div class="mt-6">
                            <a href="{{ route('reports.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors hover:opacity-90"
                               style="background:#1F4E79;color:#FFFFFF;">
                                Create your first report
                            </a>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y" style="divide-color:#eaeff5;">
                            <thead style="background:#F5F7FA;">
                                <tr>
                                    @php
                                        $columns = [
                                            'id' => 'ID',
                                            'location' => 'Location',
                                            'category' => 'Category',
                                            'status' => 'Status',
                                            'created_at' => 'Date'
                                        ];
                                    @endphp
                                    
                                    @foreach($columns as $key => $label)
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider group cursor-pointer hover:bg-gray-100 transition-colors" style="color:#7F8C8D;">
                                        <a href="{{ route('reports.index', array_merge(request()->query(), ['sort_by' => $key, 'sort_direction' => request('sort_by') === $key && request('sort_direction') === 'asc' ? 'desc' : 'asc', 'page' => 1])) }}" 
                                           class="flex items-center gap-1 w-full h-full">
                                            {{ $label }}
                                            @if(request('sort_by', 'created_at') === $key)
                                                <span class="text-indigo-600 font-bold bg-indigo-50 rounded px-1">{{ request('sort_direction', 'desc') === 'asc' ? '↑' : '↓' }}</span>
                                            @elseif(request('sort_by') === null && $key === 'created_at')
                                                 {{-- Default sort --}}
                                                <span class="text-indigo-600 font-bold bg-indigo-50 rounded px-1">↓</span>
                                            @else
                                                <span class="text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity">↕</span>
                                            @endif
                                        </a>
                                    </th>
                                    @endforeach
                                    <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider" style="color:#7F8C8D;">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y bg-white" style="divide-color:#eaeff5;">
                                @foreach ($reports as $report)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" style="color:#1F4E79;">
                                            #{{ $report->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <div class="font-medium">{{ $report->room?->block?->block_name ?? '-' }}</div>
                                            <div class="text-xs text-gray-500">{{ $report->room?->room_name ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border"
                                                style="background:#FFFFFF;border-color:#D7DDE5;color:#2C3E50;">
                                                {{ $report->category->name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border"
                                                style="
                                                @if($report->status === 'Completed') background:#D1F2EB;color:#1ABC9C;border-color:transparent
                                                @elseif($report->status === 'In_Progress') background:#EBF5FB;color:#3498DB;border-color:transparent
                                                @elseif($report->status === 'Assigned') background:#F4ECF7;color:#9B59B6;border-color:transparent
                                                @else background:#F5F7FA;color:#7F8C8D;border-color:#D7DDE5 @endif
                                                ">
                                                {{ str_replace('_', ' ', $report->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $report->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <a href="{{ route('reports.show', $report) }}" 
                                               class="text-sm hover:underline"
                                               style="color:#3498DB;">
                                                Details
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t" style="border-color:#eaeff5;">
                        {{ $reports->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@php
    $pageTitle = 'Report Details';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'My Reports', 'url' => route('reports.index')],
        ['label' => 'Report #' . $report->id],
    ];
@endphp

@section('content')
    <div class="py-10 space-y-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Top Navigation -->
            <div class="mb-6">
                <a href="{{ route('reports.index') }}" 
                   class="inline-flex items-center gap-2 text-sm font-medium hover:underline transition-colors"
                   style="color:#3498DB;">
                    ← Back to My Reports
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border overflow-hidden" style="border-color:#D7DDE5;">
                <!-- Header Section -->
                <div class="p-6 md:p-8 border-b" 
                     style="background:linear-gradient(to right, #F8F9F9, #FFFFFF); border-color:#eaeff5;">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <h2 class="text-3xl font-bold" style="color:#2C3E50;">Report #{{ $report->id }}</h2>
                                <span class="px-3 py-1 text-sm font-semibold rounded-full border"
                                    style="
                                    @if($report->status === 'Completed') background:#D1F2EB;color:#1ABC9C;border-color:transparent
                                    @elseif($report->status === 'In_Progress') background:#EBF5FB;color:#3498DB;border-color:transparent
                                    @elseif($report->status === 'Assigned') background:#F4ECF7;color:#9B59B6;border-color:transparent
                                    @else background:#F5F7FA;color:#7F8C8D;border-color:#D7DDE5 @endif
                                    ">
                                    {{ str_replace('_', ' ', $report->status) }}
                                </span>
                            </div>
                            <p class="text-sm" style="color:#7F8C8D;">
                                Submitted on {{ $report->created_at->format('F d, Y \a\t h:i A') }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full uppercase tracking-wide border"
                                style="
                                @if($report->urgency === 'High') background:#FADBD8;color:#C0392B;border-color:transparent
                                @elseif($report->urgency === 'Medium') background:#FCF3CF;color:#F39C12;border-color:transparent
                                @else background:#D1F2EB;color:#1ABC9C;border-color:transparent @endif
                                ">
                                {{ $report->urgency }} Urgency
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-10">
                    <!-- Left Column: Details -->
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider mb-1" style="color:#95A5A6;">Location</h3>
                            <p class="text-lg font-medium" style="color:#2C3E50;">
                                {{ $report->room->block->block_name }} - {{ $report->room->room_name }}
                            </p>
                            <p class="text-sm" style="color:#7F8C8D;">Floor {{ $report->room->floor_number }}</p>
                        </div>

                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider mb-1" style="color:#95A5A6;">Category</h3>
                            <p class="text-lg" style="color:#2C3E50;">{{ $report->category->name }}</p>
                        </div>

                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider mb-1" style="color:#95A5A6;">Description</h3>
                            <div class="p-4 rounded-xl border bg-gray-50 text-gray-700 whitespace-pre-wrap" 
                                 style="border-color:#eaeff5;">{{ $report->description }}</div>
                        </div>

                        @if($report->technician)
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider mb-1" style="color:#95A5A6;">Assigned Technician</h3>
                            <div class="flex items-center gap-3 p-3 rounded-xl border" style="border-color:#eaeff5;">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">
                                    {{ substr($report->technician->name, 0, 1) }}
                                </div>
                                <span class="font-medium" style="color:#2C3E50;">{{ $report->technician->name }}</span>
                            </div>
                        </div>
                        @endif

                        @if($report->resolution_notes)
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider mb-1" style="color:#95A5A6;">Resolution Notes</h3>
                            <div class="p-4 rounded-xl border bg-green-50 text-gray-700 whitespace-pre-wrap"
                                 style="border-color:#A3E4D7;">{{ $report->resolution_notes }}</div>
                        </div>
                        @endif
                    </div>

                    <!-- Right Column: Timeline & Attachments -->
                    <div class="space-y-8">
                         <!-- Timeline Component -->
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider mb-4" style="color:#95A5A6;">Status History</h3>
                            <div class="relative pl-4 border-l-2 space-y-6" style="border-color:#eaeff5;">
                                <!-- Submitted -->
                                <div class="relative">
                                    <div class="absolute -left-[21px] top-1 w-4 h-4 rounded-full border-2 bg-white"
                                         style="border-color:#1ABC9C;"></div>
                                    <h4 class="text-sm font-semibold" style="color:#2C3E50;">Report Submitted</h4>
                                    <p class="text-xs text-gray-500">{{ $report->created_at->format('M d, h:i A') }}</p>
                                </div>

                                <!-- Completed (Conditional) -->
                                @if($report->status === 'Completed')
                                    <div class="relative">
                                        <div class="absolute -left-[21px] top-1 w-4 h-4 rounded-full border-2 bg-white"
                                            style="border-color:#1ABC9C;"></div>
                                        <h4 class="text-sm font-semibold" style="color:#2C3E50;">Issue Resolved</h4>
                                        <p class="text-xs text-gray-500">
                                            {{ $report->completed_at ? $report->completed_at->format('M d, h:i A') : 'Recently' }}
                                        </p>
                                    </div>
                                @elseif(in_array($report->status, ['Assigned', 'In_Progress']))
                                     <div class="relative">
                                        <div class="absolute -left-[21px] top-1 w-4 h-4 rounded-full border-2 bg-white"
                                            style="border-color:#3498DB;"></div>
                                        <h4 class="text-sm font-semibold" style="color:#2C3E50;">Currently In Progress</h4>
                                        <p class="text-xs text-gray-500">Technician is working on it</p>
                                    </div>
                                @endif
                                
                                <div class="relative">
                                    <div class="absolute -left-[21px] top-1 w-4 h-4 rounded-full border-2 bg-white"
                                         style="border-color:#D7DDE5;"></div>
                                    <h4 class="text-sm font-semibold text-gray-400">Archived</h4>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider mb-3" style="color:#95A5A6;">Attachments</h3>
                            @if($report->attachments->isEmpty())
                                <div class="p-4 rounded-xl border text-center text-sm text-gray-400 border-dashed"
                                     style="border-color:#D7DDE5;">
                                    No photos or documents attached
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach($report->attachments as $attachment)
                                        <div class="group rounded-xl border overflow-hidden transition-shadow hover:shadow-md"
                                             style="border-color:#eaeff5;">
                                            @if(str_starts_with($attachment->file_type, 'image/'))
                                                <div class="aspect-w-16 aspect-h-9 bg-gray-100">
                                                     <img src="{{ asset('storage/' . $attachment->file_path) }}" 
                                                          alt="{{ $attachment->file_name }}" 
                                                          class="w-full h-48 object-cover">
                                                </div>
                                            @endif
                                            <div class="p-3 bg-white">
                                                <p class="text-sm font-medium truncate" style="color:#2C3E50;">{{ $attachment->file_name }}</p>
                                                <div class="flex items-center justify-between mt-1">
                                                    <span class="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-500">
                                                        {{ $attachment->attachment_type === 'REPORTER_PROOF' ? 'Proof' : 'Technician Update' }}
                                                    </span>
                                                    <span class="text-xs text-gray-400">{{ $attachment->uploaded_at->format('M d') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
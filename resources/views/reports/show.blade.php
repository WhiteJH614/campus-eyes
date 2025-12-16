<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Report Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <a href="{{ route('reports.index') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
                            ← Back to My Reports
                        </a>
                    </div>

                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-2xl font-semibold">Report #{{ $report->id }}</h2>
                            <p class="text-gray-500 dark:text-gray-400">
                                Submitted on {{ $report->created_at->format('F d, Y \a\t h:i A') }}
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <span class="px-3 py-1 text-sm font-semibold rounded-full 
                                @if($report->urgency === 'High') bg-red-100 text-red-800
                                @elseif($report->urgency === 'Medium') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ $report->urgency }} Urgency
                            </span>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full 
                                @if($report->status === 'Completed') bg-green-100 text-green-800
                                @elseif($report->status === 'In_Progress') bg-blue-100 text-blue-800
                                @elseif($report->status === 'Assigned') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ str_replace('_', ' ', $report->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Location</h3>
                                <p class="mt-1 text-lg">
                                    {{ $report->room->block->block_name }} - {{ $report->room->room_name }}
                                    <span class="text-gray-500">(Floor {{ $report->room->floor_number }})</span>
                                </p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Category</h3>
                                <p class="mt-1 text-lg">{{ $report->category->name }}</p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Description</h3>
                                <p class="mt-1 text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $report->description }}</p>
                            </div>

                            @if($report->technician)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Assigned Technician</h3>
                                <p class="mt-1 text-lg">{{ $report->technician->name }}</p>
                            </div>
                            @endif

                            @if($report->resolution_notes)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Resolution Notes</h3>
                                <p class="mt-1 text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $report->resolution_notes }}</p>
                            </div>
                            @endif

                            @if($report->completed_at)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Completed On</h3>
                                <p class="mt-1 text-lg">{{ $report->completed_at->format('F d, Y \a\t h:i A') }}</p>
                            </div>
                            @endif
                        </div>

                        <!-- Right Column - Attachments -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Attachments</h3>
                            @if($report->attachments->isEmpty())
                                <p class="text-gray-500 dark:text-gray-400">No attachments</p>
                            @else
                                <div class="space-y-3">
                                    @foreach($report->attachments as $attachment)
                                        <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
                                            @if(str_starts_with($attachment->file_type, 'image/'))
                                                <img src="{{ asset('storage/' . $attachment->file_path) }}" 
                                                     alt="{{ $attachment->file_name }}" 
                                                     class="w-full h-48 object-cover">
                                            @endif
                                            <div class="p-3 bg-gray-50 dark:bg-gray-700">
                                                <p class="text-sm font-medium truncate">{{ $attachment->file_name }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $attachment->attachment_type === 'REPORTER_PROOF' ? 'Reporter Proof' : 'Technician Proof' }}
                                                    • {{ $attachment->uploaded_at->format('M d, Y') }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="mt-8 pt-6 border-t dark:border-gray-700">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-4">Status Timeline</h3>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white text-sm">✓</div>
                                <span class="ml-2 text-sm">Submitted</span>
                            </div>
                            <div class="flex-1 h-1 {{ in_array($report->status, ['Assigned', 'In_Progress', 'Completed']) ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full {{ in_array($report->status, ['Assigned', 'In_Progress', 'Completed']) ? 'bg-green-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500' }} flex items-center justify-center text-sm">
                                    {{ in_array($report->status, ['Assigned', 'In_Progress', 'Completed']) ? '✓' : '2' }}
                                </div>
                                <span class="ml-2 text-sm">Assigned</span>
                            </div>
                            <div class="flex-1 h-1 {{ in_array($report->status, ['In_Progress', 'Completed']) ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full {{ in_array($report->status, ['In_Progress', 'Completed']) ? 'bg-green-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500' }} flex items-center justify-center text-sm">
                                    {{ in_array($report->status, ['In_Progress', 'Completed']) ? '✓' : '3' }}
                                </div>
                                <span class="ml-2 text-sm">In Progress</span>
                            </div>
                            <div class="flex-1 h-1 {{ $report->status === 'Completed' ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full {{ $report->status === 'Completed' ? 'bg-green-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500' }} flex items-center justify-center text-sm">
                                    {{ $report->status === 'Completed' ? '✓' : '4' }}
                                </div>
                                <span class="ml-2 text-sm">Completed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

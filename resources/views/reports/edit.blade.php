@extends('layouts.app')

@php
    $pageTitle = 'Edit Report';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'My Reports', 'url' => route('reports.index')],
        ['label' => 'Report #' . $report->id, 'url' => route('reports.show', $report)],
        ['label' => 'Edit'],
    ];
@endphp

@section('content')
<div class="py-8">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm border overflow-hidden" style="border-color:#D7DDE5;">
            <!-- Header -->
            <div class="p-6 md:p-8 border-b" 
                 style="background:linear-gradient(to right, #F8F9F9, #FFFFFF); border-color:#eaeff5;">
                <h2 class="text-2xl font-bold" style="color:#2C3E50;">Edit Report #{{ $report->id }}</h2>
                <p class="mt-1 text-sm" style="color:#7F8C8D;">Update the details of your maintenance request.</p>
            </div>

            <!-- Form -->
            <form action="{{ route('reports.update', $report) }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8 space-y-6">
                @csrf
                @method('PUT')

                <!-- Location Section -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider border-b pb-2" style="color:#95A5A6; border-color:#eaeff5;">Location</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="block_id" class="block text-sm font-medium text-gray-700 mb-1">Block / Building</label>
                            <select id="block_id" 
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors"
                                    onchange="loadRooms(this.value)">
                                <option value="">Select a Block</option>
                                @foreach($blocks as $block)
                                    <option value="{{ $block->id }}" {{ $report->room->block_id == $block->id ? 'selected' : '' }}>
                                        {{ $block->block_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="room_id" class="block text-sm font-medium text-gray-700 mb-1">Room / Area</label>
                            <select id="room_id" name="room_id" 
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors" required>
                                <option value="">Select a Room</option>
                                {{-- Options populated via JS on load --}}
                            </select>
                            @error('room_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Issue Details -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider border-b pb-2" style="color:#95A5A6; border-color:#eaeff5;">Issue Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select id="category_id" name="category_id" 
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $report->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="urgency" class="block text-sm font-medium text-gray-700 mb-1">Urgency Level</label>
                            <select id="urgency" name="urgency" 
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors" required>
                                <option value="Low" {{ $report->urgency == 'Low' ? 'selected' : '' }}>Low - Minor cosmetic issue</option>
                                <option value="Medium" {{ $report->urgency == 'Medium' ? 'selected' : '' }}>Medium - Affects usability</option>
                                <option value="High" {{ $report->urgency == 'High' ? 'selected' : '' }}>High - Safety hazard or critical failure</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="description" name="description" rows="4" 
                                  class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors"
                                  placeholder="Describe the issue in detail..." required>{{ old('description', $report->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Attachment (Optional)</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="attachment" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>Upload a new file</span>
                                        <input id="attachment" name="attachment" type="file" class="sr-only" accept="image/*">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                            </div>
                        </div>
                        @if($report->attachments->isNotEmpty())
                            <p class="mt-2 text-sm text-gray-500">Current attachment: {{ $report->attachments->first()->file_name }}</p>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t" style="border-color:#eaeff5;">
                    <a href="{{ route('reports.show', $report) }}" 
                       class="px-4 py-2 text-sm font-medium rounded-lg text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 text-sm font-medium rounded-lg text-white shadow-sm hover:opacity-90 transition-opacity"
                            style="background:#1F4E79;">
                        Update Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function loadRooms(blockId) {
        const roomSelect = document.getElementById('room_id');
        roomSelect.innerHTML = '<option value="">Loading...</option>';
        
        if (!blockId) {
            roomSelect.innerHTML = '<option value="">Select a Room</option>';
            return;
        }

        fetch(`{{ url('/reports/rooms') }}/${blockId}`)
            .then(response => response.json())
            .then(rooms => {
                roomSelect.innerHTML = '<option value="">Select a Room</option>';
                rooms.forEach(room => {
                    const selected = room.id == {{ $report->room_id }} ? 'selected' : '';
                    roomSelect.innerHTML += `<option value="${room.id}" ${selected}>${room.room_name} (Floor ${room.floor_number})</option>`;
                });
            })
            .catch(error => {
                console.error('Error:', error);
                roomSelect.innerHTML = '<option value="">Error loading rooms</option>';
            });
    }

    // Initial load
    document.addEventListener('DOMContentLoaded', function() {
        if(document.getElementById('block_id').value) {
            loadRooms(document.getElementById('block_id').value);
        }
    });
</script>
@endpush
@endsection

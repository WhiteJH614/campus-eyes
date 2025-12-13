@extends('layouts.app')

@php
    /** @var \App\Models\Report $job */

    $pageTitle = 'Task Detail';
    $ticketLabel = 'Ticket ' . $job->id;

    $locationText = trim(
        ($job->room->block->campus->campus_name ?? '') . ', ' .
        ($job->room->block->block_name ?? '') . ', ' .
        ($job->room->room_name ?? '')
        ,
        ' ,'
    );

    $urgency = $job->urgency ?? 'Medium';
    $status = $job->status ?? 'Assigned';

    $urgencyChip = match (strtolower($urgency)) {
        'high' => ['High urgency', '#E74C3C', '#FFFFFF'],
        'low' => ['Low urgency', '#2ECC71', '#FFFFFF'],
        default => ['Medium urgency', '#F1C40F', '#2C3E50'],
    };

    $statusChip = match ($status) {
        'In_Progress' => ['In Progress', '#3498DB', '#FFFFFF'],
        'Completed' => ['Completed', '#27AE60', '#FFFFFF'],
        'Escalated' => ['Escalated', '#E74C3C', '#FFFFFF'],
        default => [$status, '#F39C12', '#FFFFFF'],
    };

    // Attachments
    $beforePhotoPath = optional(
        $job->attachments->firstWhere('attachment_type', 'REPORTER_PROOF')
    )->file_path;

    $afterPhotoPath = optional(
        $job->attachments->firstWhere('attachment_type', 'TECHNICIAN_PROOF')
    )->file_path;
@endphp

@section('content')
    <section class="space-y-6">
        {{-- Top card: basic info --}}
        <div class="rounded-2xl shadow-sm border p-6" style="background:#FFFFFF;border-color:#D7DDE5;">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-semibold" style="color:#2C3E50;">
                        {{ $ticketLabel }}
                    </h1>
                    <p class="text-sm" style="color:#7F8C8D;">Technician view and actions.</p>
                </div>
                <div class="flex flex-wrap gap-2 text-sm">
                    <span class=" px-3 py-1 rounded-full font-semibold"
                    style="background-color: {{ $urgencyChip[1] }}; color: {{ $urgencyChip[2] }};">
                    {{ $urgencyChip[0] }}
                    </span>
                    <span class="px-3 py-1 rounded-full font-semibold"
                        style="background-color: {{ $statusChip[1] }}; color: {{ $statusChip[2] }};">
                        {{ $statusChip[0] }}
                    </span>
                    @if($job->due_at)
                        <span class="px-3 py-1 rounded-full font-semibold" style="background-color:#F1C40F;color:#2C3E50;">
                            Due: {{ $job->due_at->format('Y-m-d H:i') }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <div class="text-sm font-semibold" style="color:#2C3E50;">Location</div>
                    <div style="color:#2C3E50;">{{ $locationText ?: '-' }}</div>
                </div>
                <div class="space-y-2">
                    <div class="text-sm font-semibold" style="color:#2C3E50;">Category</div>
                    <div style="color:#2C3E50;">{{ $job->category->name ?? '-' }}</div>
                </div>
                <div class="space-y-2">
                    <div class="text-sm font-semibold" style="color:#2C3E50;">Reported at</div>
                    <div style="color:#2C3E50;">
                        {{ $job->created_at?->format('Y-m-d H:i') ?? '-' }}
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="text-sm font-semibold" style="color:#2C3E50;">Assigned at</div>
                    <div style="color:#2C3E50;">
                        {{ $job->assigned_at?->format('Y-m-d H:i') ?? '-' }}
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <div class="text-sm font-semibold mb-1" style="color:#2C3E50;">Description</div>
                <p class="text-sm" style="color:#2C3E50;">{{ $job->description ?? '-' }}</p>
            </div>

            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <div class="text-sm font-semibold mb-1" style="color:#2C3E50;">Before photo (reporter)</div>
                    <div class="rounded-lg border aspect-video flex items-center justify-center overflow-hidden"
                        style="border-color:#D7DDE5;background:#F5F7FA;">
                        @if($beforePhotoPath)
                            <img src="{{ asset('storage/' . $beforePhotoPath) }}" alt="Before photo"
                                class="h-full w-full object-cover">
                        @else
                            <span class="text-sm" style="color:#7F8C8D;">No photo uploaded</span>
                        @endif
                    </div>
                </div>
                <div>
                    <div class="text-sm font-semibold mb-1" style="color:#2C3E50;">After repair photo</div>
                    <div class="rounded-lg border aspect-video flex items-center justify-center overflow-hidden"
                        style="border-color:#D7DDE5;background:#F5F7FA;">
                        @if($afterPhotoPath)
                            <img src="{{ asset('storage/' . $afterPhotoPath) }}" alt="Technician proof"
                                class="h-full w-full object-cover">
                        @else
                            <span class="text-sm" style="color:#7F8C8D;">No technician proof yet</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Status quick actions --}}
        <div class="rounded-2xl shadow-sm border p-6" style="background:#FFFFFF;border-color:#D7DDE5;">
            <h2 class="text-lg font-semibold mb-3" style="color:#2C3E50;">Status updates</h2>

            <div class="grid sm:grid-cols-2 gap-4">
                {{-- Quick status change (no proof) --}}
                <form method="post" action="{{ route('technician.update_status', $job->id) }}" class="space-y-3"
                    onsubmit="return confirm('Update status for this task?');">
                    @csrf

                    <label class="text-sm font-medium" style="color:#2C3E50;">Set status to</label>
                    <div class="grid sm:grid-cols-2 gap-2">
                        <button type="submit" name="status" value="In_Progress"
                            class="rounded-lg border px-3 py-2 text-sm font-semibold text-left"
                            style="border-color:#D7DDE5;color:#2C3E50;">
                            In Progress
                        </button>

                        <button type="submit" name="status" value="Escalated"
                            class="rounded-lg border px-3 py-2 text-sm font-semibold text-left"
                            style="border-color:#D7DDE5;color:#2C3E50;">
                            Escalated / Cannot Resolve
                        </button>
                    </div>

                    <p class="text-xs" style="color:#7F8C8D;">
                        Use these buttons when you only want to change the status without closing the task.
                    </p>
                </form>

                {{-- Complete job with proof --}}
                <form method="post" action="{{ route('technician.complete_job', $job->id) }}" enctype="multipart/form-data"
                    class="space-y-3" onsubmit="return confirm('Mark this job as completed and upload proof image?');">
                    @csrf

                    <label class="text-sm font-medium" style="color:#2C3E50;">Complete job</label>

                    <div class="space-y-2">
                        <label class="text-xs font-medium" style="color:#2C3E50;">After repair photo (proof)</label>
                        <input type="file" name="proof_image" accept="image/*" class="rounded-lg px-3 py-2 border w-full"
                            style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" required>
                        <p class="text-xs" style="color:#7F8C8D;">
                            Required when closing the task. This will be stored as TECHNICIAN_PROOF.
                        </p>
                    </div>

                    <div class="space-y-2">
                        <label for="notes" class="text-xs font-medium" style="color:#2C3E50;">Resolution notes</label>
                        <textarea id="notes" name="resolution_notes" rows="3"
                            class="w-full rounded-lg px-3 py-2 border focus:outline-none"
                            style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" required></textarea>
                    </div>

                    <button type="submit" class="rounded-lg px-4 py-2 font-semibold text-sm"
                        style="background:#27AE60;color:#FFFFFF;">
                        Mark as Completed
                    </button>
                </form>
            </div>

            <div class="mt-4 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between text-sm"
                style="color:#7F8C8D;">
                <div>Only the assigned technician or admin can update this task.</div>
                <div class="flex gap-2">
                    <a href="{{ route('technician.my_jobs') }}" class="rounded-lg px-4 py-2 font-semibold border"
                        style="border-color:#D7DDE5;color:#1F4E79;">
                        Back to jobs
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
@extends('layouts.app')

@php
/** @var \App\Models\Report $job */

$pageTitle = 'Task Detail';
$ticketLabel = 'Report ID ' . $job->id;

$locationText = trim(
    ($job->room->block->campus->campus_name ?? '') . ', ' .
    ($job->room->block->block_name ?? '') . ', ' .
    ($job->room->room_name ?? '')
    ,
    ' ,'
);

$urgency = $job->urgency ?? 'Medium';
$status = $job->status ?? 'Assigned';
$isInProgress = $status === 'In_Progress';
$isCompleted = $status === 'Completed';

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

$afterList = ($afterPhotos ?? collect())
    ->map(fn($a) => ['id' => $a->id, 'url' => asset('storage/' . $a->file_path)])
    ->values()
    ->toArray();
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

                    <div class="rounded-lg border overflow-hidden relative"
                        style="border-color:#D7DDE5;background:#F5F7FA;">
                        @if(count($afterList) > 0)
                            <div class="aspect-video flex items-center justify-center">
                                <img id="afterImg" src="{{ $afterList[0]['url'] ?? '' }}" alt="Technician proof"
                                    class="h-full w-full object-cover">
                            </div>

                            {{-- arrows --}}
                            @if(count($afterList) > 1)
                                <button type="button" id="afterPrev"
                                    class="absolute left-3 top-1/2 -translate-y-1/2 h-10 w-10 rounded-full font-bold"
                                    style="background:rgba(255,255,255,0.9);color:#2C3E50;border:1px solid #D7DDE5;">
                                    &lsaquo;
                                </button>
                                <button type="button" id="afterNext"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 h-10 w-10 rounded-full font-bold"
                                    style="background:rgba(255,255,255,0.9);color:#2C3E50;border:1px solid #D7DDE5;">
                                    &rsaquo;
                                </button>
                            @endif
                        @else
                            <div class="aspect-video flex items-center justify-center">
                                <span class="text-sm" style="color:#7F8C8D;">No technician proof yet</span>
                            </div>
                        @endif
                    </div>

                    {{-- dots --}}
                    @if(count($afterList) > 1)
                        <div id="afterDots" class="flex justify-center gap-2 mt-3"></div>
                    @endif
                    @if(count($afterList) > 0)
                        <form id="delete-after-form"
                            method="post"
                            action="{{ route('technician.delete_after', [$job->id, $afterList[0]['id'] ?? 0]) }}"
                            class="mt-2"
                            onsubmit="return confirm('Remove this photo?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs rounded-lg px-3 py-2 border font-semibold"
                                style="border-color:#D7DDE5;color:#C0392B;background:#FFFFFF;">
                                Remove this photo
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        @if(count($afterList) > 1)
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const photos = @json($afterList);
                    let idx = 0;

                    const img = document.getElementById('afterImg');
                    const prev = document.getElementById('afterPrev');
                    const next = document.getElementById('afterNext');
                    const dotsWrap = document.getElementById('afterDots');
                    const deleteForm = document.getElementById('delete-after-form');
                    const baseDelete = deleteForm ? deleteForm.action.replace(/\/[0-9]+$/, '') : null;

                    const render = () => {
                        if (!img) return;
                        img.src = photos[idx].url;

                        if (deleteForm && baseDelete) {
                            deleteForm.action = baseDelete + '/' + photos[idx].id;
                        }

                        if (dotsWrap) {
                            dotsWrap.innerHTML = '';
                            photos.forEach((_, i) => {
                                const b = document.createElement('button');
                                b.type = 'button';
                                b.setAttribute('aria-label', 'View photo ' + (i + 1));
                                b.style.width = '10px';
                                b.style.height = '10px';
                                b.style.borderRadius = '999px';
                                b.style.border = '1px solid #D7DDE5';
                                b.style.background = i === idx ? '#1F4E79' : '#FFFFFF';
                                b.addEventListener('click', () => { idx = i; render(); });
                                dotsWrap.appendChild(b);
                            });
                        }
                    };

                    prev?.addEventListener('click', () => { idx = (idx - 1 + photos.length) % photos.length; render(); });
                    next?.addEventListener('click', () => { idx = (idx + 1) % photos.length; render(); });

                    render();
                });
            </script>
        @endif

        {{-- Status quick actions --}}
        @if($isCompleted)
            <div class="rounded-2xl shadow-sm border p-6" style="background:#FFFFFF;border-color:#D7DDE5;">
                <h2 class="text-xl font-semibold mb-2" style="color:#2C3E50;">Status updates</h2>
                <p class="text-sm" style="color:#7F8C8D;">This task is completed. Status changes and uploads are locked.</p>
            </div>
        @else
            <div class="rounded-2xl shadow-sm border p-6" style="background:#FFFFFF;border-color:#D7DDE5;">
                <h2 class="text-xl font-semibold mb-4" style="color:#2C3E50;">Status updates</h2>

                <div class="grid sm:grid-cols-2 gap-4">
                    {{-- Start job --}}
                    <form method="post" action="{{ route('technician.update_status', $job->id) }}"
                        onsubmit="return confirm('Move this task to In Progress?');" class="rounded-2xl border p-4"
                        style="border-color:#D7DDE5;background:#F9FBFF;">
                        @csrf
                        <div class="text-sm font-semibold mb-2" style="color:#2C3E50;">Quick status</div>

                        <button type="submit" name="status" value="In_Progress"
                            @if($isInProgress) disabled @endif
                            class="w-full rounded-xl px-4 py-3 font-semibold cursor-pointer"
                            style="background:{{ $isInProgress ? '#D7DDE5' : '#3498DB' }};color:#FFFFFF;">
                            {{ $isInProgress ? 'Already In Progress' : 'Start / In Progress' }}
                        </button>

                        <p class="text-xs mt-2" style="color:#7F8C8D;">
                            Use when you start working on this task.
                        </p>
                    </form>

                    {{-- Complete job with proof --}}
                    @if($isInProgress)
                        <form method="post" action="{{ route('technician.complete_job', $job->id) }}" enctype="multipart/form-data"
                            class="space-y-3" onsubmit="return confirm('Mark this job as completed and upload proof image?');">
                            @csrf

                            <label class="text-sm font-medium" style="color:#2C3E50;">Complete job</label>

                            <div class="space-y-2">
                                <label class="text-xs font-medium" style="color:#2C3E50;">After repair photo (proof)</label>
                                <label for="proof-images"
                                    class="block w-full rounded-xl border-2 border-dashed px-4 py-6 cursor-pointer text-center"
                                    style="border-color:#B0BEC5;background:#F9FBFF;color:#2C3E50;">
                                    <div class="text-sm font-semibold">Click to choose photos</div>
                                    <div class="text-xs mt-1" style="color:#546E7A;">PNG, JPG or JPEG - Multiple files allowed</div>
                                </label>

                                <input id="proof-images" type="file" name="proof_images[]" accept="image/*" multiple required
                                    class="hidden">

                                <div id="after-photo-preview" class="grid grid-cols-3 sm:grid-cols-4 gap-2"></div>

                                <p class="text-xs" style="color:#7F8C8D;">Required when closing the task. Stored as technician proof.</p>
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
                    @else
                        <div class="rounded-2xl border p-4" style="border-color:#D7DDE5;background:#F5F7FA;">
                            <div class="text-sm font-semibold mb-2" style="color:#2C3E50;">Complete job</div>
                            <p class="text-xs" style="color:#7F8C8D;">Set status to In Progress to upload proof images and close this task.</p>
                        </div>
                    @endif
                </div>

                <div class="mt-4 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between text-sm"
                    style="color:#7F8C8D;">
                    <div>Only the assigned technician or admin can update this task.</div>
                    <div class="flex gap-2">
                        <a href="{{ route('technician.tasks') }}" class="rounded-lg px-4 py-2 font-semibold border"
                            style="border-color:#D7DDE5;color:#1F4E79;">
                            Back to jobs
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('proof-images');
            const preview = document.getElementById('after-photo-preview');

            if (!input || !preview) return;

            let buffer = new DataTransfer();

            const syncAndRender = () => {
                input.files = buffer.files;
                preview.innerHTML = '';

                Array.from(buffer.files).forEach((file, index) => {
                    if (!file.type.startsWith('image/')) return;

                    const wrapper = document.createElement('div');
                    wrapper.style.position = 'relative';
                    wrapper.style.border = '#D7DDE5 1px solid';
                    wrapper.style.borderRadius = '8px';
                    wrapper.style.overflow = 'hidden';
                    wrapper.style.background = '#F5F7FA';
                    wrapper.style.aspectRatio = '4 / 3';
                    wrapper.style.display = 'flex';
                    wrapper.style.alignItems = 'center';
                    wrapper.style.justifyContent = 'center';

                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.alt = 'After repair preview';
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'cover';
                    img.onload = () => URL.revokeObjectURL(img.src);

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.setAttribute('aria-label', 'Remove photo ' + (index + 1));
                    removeBtn.textContent = 'x';
                    removeBtn.style.position = 'absolute';
                    removeBtn.style.top = '6px';
                    removeBtn.style.right = '6px';
                    removeBtn.style.height = '24px';
                    removeBtn.style.width = '24px';
                    removeBtn.style.borderRadius = '999px';
                    removeBtn.style.border = '1px solid #D7DDE5';
                    removeBtn.style.background = '#FFFFFF';
                    removeBtn.style.color = '#2C3E50';
                    removeBtn.style.fontWeight = 'bold';
                    removeBtn.style.cursor = 'pointer';
                    removeBtn.addEventListener('click', () => {
                        const nextBuffer = new DataTransfer();
                        Array.from(buffer.files).forEach((f, i) => {
                            if (i !== index) {
                                nextBuffer.items.add(f);
                            }
                        });
                        buffer = nextBuffer;
                        syncAndRender();
                    });

                    wrapper.appendChild(removeBtn);
                    wrapper.appendChild(img);
                    preview.appendChild(wrapper);
                });
            };

            input.addEventListener('change', () => {
                const nextBuffer = new DataTransfer();
                Array.from(buffer.files).forEach((file) => nextBuffer.items.add(file));
                Array.from(input.files || []).forEach((file) => nextBuffer.items.add(file));
                buffer = nextBuffer;
                syncAndRender();
            });

            syncAndRender();
        });
    </script>
@endsection

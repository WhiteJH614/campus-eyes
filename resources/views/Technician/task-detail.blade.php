@extends('layouts.app')

@php
    $pageTitle = 'Task Detail';
    $jobId = request()->route('id') ?? ($job->id ?? null);
@endphp

@section('content')
    <section class="space-y-6" x-data="taskDetailPage({{ (int) $jobId }})" x-init="load()">
        {{-- Top card: basic info --}}
        <div class="flex justify-start mb-3">
            <a href="{{ route('technician.tasks') }}"
                class="rounded-lg px-4 py-2 font-semibold border text-sm"
                style="border-color:#D7DDE5;color:#1F4E79;background:#FFFFFF;">
                Back to jobs
            </a>
        </div>

        <div class="rounded-2xl shadow-sm border p-6" style="background:#FFFFFF;border-color:#D7DDE5;">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-semibold" style="color:#000000;">
                        Report ID <span x-text="job.id || '-'"></span>
                    </h1>
                    <p class="text-sm" style="color:#000000;">Technician view and actions.</p>
                </div>
            <div class="flex flex-wrap gap-2 text-sm">
                <span class=" px-3 py-1 rounded-full font-semibold" :style="chipStyle(urgencyChip.bg, urgencyChip.fg)"
                    x-text="urgencyChip.label"></span>
                <span class="px-3 py-1 rounded-full font-semibold" :style="chipStyle(statusChip.bg, statusChip.fg)"
                    x-text="statusChip.label"></span>
                <span class="px-3 py-1 rounded-full font-semibold"
                    :style="job.is_overdue ? 'background:#E74C3C;color:#FFFFFF;' : 'background:#27AE60;color:#FFFFFF;'">
                    <span x-text="job.overdue_label"></span>
                </span>
                <template x-if="job.status === 'Completed' && job.completed_at">
                    <span class="px-3 py-1 rounded-full font-semibold" style="background-color:#27AE60;color:#FFFFFF;">
                        Completed at: <span x-text="job.completed_at"></span>
                    </span>
                </template>
                <template x-if="job.status !== 'Completed' && job.due_at">
                    <span class="px-3 py-1 rounded-full font-semibold" style="background-color:#F1C40F;color:#000000;">
                        Due: <span x-text="job.due_at"></span>
                    </span>
                </template>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <div class="text-sm font-semibold" style="color:#000000;">Location</div>
                    <div style="color:#000000;" x-text="job.location || '-'"></div>
                </div>
                <div class="space-y-2">
                    <div class="text-sm font-semibold" style="color:#000000;">Category</div>
                    <div style="color:#000000;" x-text="job.category || '-'"></div>
                </div>
                <div class="space-y-2">
                    <div class="text-sm font-semibold" style="color:#000000;">Reported at</div>
                    <div style="color:#000000;">
                        <div x-text="job.reported_date || '-'"></div>
                        <div class="text-xs text-[#7F8C8D]" x-text="job.reported_time || ''"></div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <div class="text-sm font-semibold mb-1" style="color:#000000;">Description</div>
                <p class="text-sm" style="color:#000000;" x-text="job.description || '-'"></p>
            </div>

            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <div class="text-sm font-semibold mb-1" style="color:#000000;">Before photo (reporter)</div>
                    <div class="rounded-lg border aspect-video flex items-center justify-center overflow-hidden"
                        style="border-color:#D7DDE5;background:#F5F7FA;">
                        <template x-if="beforePhoto">
                            <img :src="beforePhoto" alt="Before photo" class="h-full w-full object-cover">
                        </template>
                        <template x-if="!beforePhoto">
                            <span class="text-sm" style="color:#000000;">No photo uploaded</span>
                        </template>
                    </div>
                </div>
                <div>
                    <div class="text-sm font-semibold mb-1" style="color:#000000;">After repair photo</div>

                    <div class="rounded-lg border overflow-hidden relative"
                        style="border-color:#D7DDE5;background:#F5F7FA;">
                        <template x-if="afterPhotos.length > 0">
                            <div class="aspect-video flex items-center justify-center relative">
                                <img :src="afterPhotos[currentAfterIndex]?.url || ''" alt="Technician proof"
                                    class="h-full w-full object-cover cursor-zoom-in"
                                    @click="openLightbox(afterPhotos, currentAfterIndex)">
                            </div>
                        </template>
                        <template x-if="afterPhotos.length === 0">
                            <div class="aspect-video flex items-center justify-center">
                                <span class="text-sm" style="color:#000000;">No technician proof yet</span>
                            </div>
                        </template>
                        <div class="absolute top-3 right-3 px-3 py-1 rounded-full text-xs font-semibold"
                            :style="job.is_overdue ? 'background:#E74C3C;color:#FFFFFF;' : 'background:#27AE60;color:#FFFFFF;'"
                            x-text="job.overdue_label">
                        </div>

                        <template x-if="afterPhotos.length > 1">
                            <button type="button"
                                class="absolute left-3 top-1/2 -translate-y-1/2 h-12 w-12 rounded-full font-bold shadow-lg"
                                style="background:#FFFFFF;color:#1F4E79;border:2px solid #1F4E79;" @click="prevAfter">
                                ‹
                            </button>
                        </template>
                        <template x-if="afterPhotos.length > 1">
                            <button type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 h-12 w-12 rounded-full font-bold shadow-lg"
                                style="background:#FFFFFF;color:#1F4E79;border:2px solid #1F4E79;" @click="nextAfter">
                                ›
                            </button>
                        </template>
                    </div>

                    <template x-if="afterPhotos.length > 1">
                        <div class="flex justify-center gap-2 mt-3">
                            <template x-for="(photo, idx) in afterPhotos" :key="photo.id">
                                <button type="button" aria-label="View photo" class="h-2.5 w-2.5 rounded-full border"
                                    :style="`border-color:#D7DDE5;background:${currentAfterIndex===idx ? '#1F4E79' : '#FFFFFF'}`"
                                    @click="currentAfterIndex = idx"></button>
                            </template>
                        </div>
                    </template>
                    <div class="mt-3 flex flex-wrap gap-3 justify-start">
                        <template x-if="afterPhotos.length > 0">
                            <form method="post"
                                :action="`/technician/tasks/${jobId}/attachments/${afterPhotos[currentAfterIndex]?.id}`"
                                onsubmit="return confirm('Remove this photo?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 rounded-lg text-xs font-semibold shadow-sm"
                                    style="background:linear-gradient(135deg,#fce4e4,#f8d7da);color:#c0392b;border:1px solid #f5c6cb;">
                                    Remove image
                                </button>
                            </form>
                        </template>
                        <template x-if="job.status === 'Completed'">
                            <form x-ref="addProofForm" method="post" :action="`/technician/tasks/${jobId}/proofs`"
                                enctype="multipart/form-data" class="flex items-center gap-2">
                                @csrf
                                <input type="file" name="proof_images[]" accept="image/*" multiple class="hidden"
                                    x-ref="addProofInput" @change="$refs.addProofForm.submit()">
                                <button type="button" class="px-4 py-2 rounded-lg text-xs font-semibold shadow-sm"
                                    style="background:linear-gradient(135deg,#e8f4ff,#d6e9ff);color:#1f4e79;border:1px solid #b6d4fe;"
                                    @click.prevent="$refs.addProofInput.click()">
                                    Add more images
                                </button>
                            </form>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status quick actions (keep server actions) --}}
        <div class="rounded-2xl shadow-sm border p-6" style="background:#FFFFFF;border-color:#D7DDE5;">
            <h2 class="text-xl font-semibold mb-4" style="color:#000000;">Status updates</h2>
            <template x-if="job.status === 'Completed'">
                <div>
                    <p class="text-sm" style="color:#000000;">This task is completed. Status changes and uploads are locked.
                    </p>
                </div>
            </template>
            <template x-if="job.status !== 'Completed'">
                <div class="grid sm:grid-cols-2 gap-4">
                    <form method="post" :action="`/technician/tasks/${jobId}/status`" x-data="{ rejectionRequired: false }"
                        onsubmit="return confirm('Update status?');" class="rounded-2xl border p-4 space-y-2"
                        style="border-color:#D7DDE5;background:#F9FBFF;">
                        @csrf
                        <div class="text-sm font-semibold" style="color:#000000;">Quick status</div>

                        <button type="submit" name="status" value="In_Progress" :disabled="job.status === 'In_Progress'"
                            class="w-full rounded-xl px-4 py-3 font-semibold cursor-pointer"
                            :style="`background:${job.status === 'In_Progress' ? '#D7DDE5' : '#3498DB'};color:#FFFFFF;`"
                            @click="rejectionRequired = false">
                            <span
                                x-text="job.status === 'In_Progress' ? 'Already In Progress' : 'Start / In Progress'"></span>
                        </button>

                        <button type="submit" name="status" value="Pending" :disabled="job.status === 'Pending'"
                            class="w-full rounded-xl px-4 py-3 font-semibold cursor-pointer"
                            :style="`background:${job.status === 'Pending' ? '#D7DDE5' : '#E74C3C'};color:#FFFFFF;`"
                            @click="rejectionRequired = true">
                            Reject / Set Pending
                        </button>

                        <div class="space-y-1">
                            <label class="text-xs font-semibold" style="color:#000000;">Reason (required when
                                rejecting)</label>
                            <textarea name="reason" rows="2" class="w-full rounded-lg px-3 py-2 border focus:outline-none"
                                style="border-color:#D7DDE5;color:#000000;background:#FFFFFF;" :required="rejectionRequired"
                                placeholder="Enter reason for rejection"></textarea>
                        </div>

                        <p class="text-xs" style="color:#000000;">
                            Use when you start working on this task or need to send it back to Pending.
                        </p>
                    </form>

                    <template x-if="job.status === 'In_Progress'">
                        <form method="post" :action="`/technician/tasks/${jobId}/complete`" enctype="multipart/form-data"
                            class="space-y-3"
                            onsubmit="return confirm('Mark this job as completed and upload proof image?');">
                            @csrf

                            <label class="text-sm font-medium" style="color:#000000;">Complete job</label>

                            <div class="space-y-2">


                                <div class="space-y-2">
                                    <label class="text-xs font-medium" style="color:#000000;">After repair photo
                                        (proof)</label>

                                    <label for="proof-images"
                                        class="block w-full rounded-xl border-2 border-dashed px-4 py-6 cursor-pointer text-center"
                                        style="border-color:#B0BEC5;background:#F9FBFF;color:#000000;"
                                        @click.prevent="$refs.proofInput.click()">
                                        <div class="text-sm font-semibold">Click to choose photos</div>
                                        <div class="text-xs mt-1" style="color:#546E7A;">PNG, JPG or JPEG - Multiple files
                                            allowed</div>
                                    </label>

                                    <input id="proof-images" x-ref="proofInput" type="file" name="proof_images[]"
                                        accept="image/*" multiple required class="hidden"
                                        x-on:change="renderProofPreview($event)">

                                    <!-- Preview grid -->
                                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                        <template x-for="(url, idx) in proofPreviewUrls" :key="idx">
                                            <div class="border rounded-lg overflow-hidden"
                                                style="border-color:#D7DDE5;background:#F5F7FA;aspect-ratio:4/3;">
                                                <img :src="url.url" alt="After repair preview"
                                                    class="w-full h-full object-cover cursor-zoom-in"
                                                    @click="openLightbox(proofPreviewUrls, idx)">
                                            </div>
                                        </template>
                                    </div>

                                    <p class="text-xs" style="color:#000000;">Required when closing the task. Stored as
                                        technician proof.</p>
                                </div>

                            </div>

                            <div class="space-y-2">
                                <label for="notes" class="text-xs font-medium" style="color:#000000;">Resolution
                                    notes</label>
                                <textarea id="notes" name="resolution_notes" rows="3"
                                    class="w-full rounded-lg px-3 py-2 border focus:outline-none"
                                    style="border-color:#D7DDE5;color:#000000;background:#FFFFFF;" required></textarea>
                            </div>

                            <button type="submit" class="rounded-lg px-4 py-2 font-semibold text-sm"
                                style="background:#27AE60;color:#FFFFFF;">
                                Mark as Completed
                            </button>
                        </form>
                    </template>
                    <template x-if="job.status !== 'In_Progress'">
                        <div class="rounded-2xl border p-4" style="border-color:#D7DDE5;background:#F5F7FA;">
                            <div class="text-sm font-semibold mb-2" style="color:#000000;">Complete job</div>
                            <p class="text-xs" style="color:#000000;">Set status to In Progress to upload proof images and
                                close this task.</p>
                        </div>
                    </template>
                </div>
            </template>

            <div class="mt-4 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between text-sm"
                style="color:#000000;">
                <div>Only the assigned technician or admin can update this task.</div>
                <div class="flex gap-2">
                    <a href="{{ route('technician.tasks') }}" class="rounded-lg px-4 py-2 font-semibold border"
                        style="border-color:#D7DDE5;color:#1F4E79;">
                        Back to jobs
                    </a>
                </div>
            </div>
        </div>
        <!-- Lightbox for zoomed images -->
        <div x-show="showLightbox" x-cloak class="fixed inset-0 bg-black/70 flex items-center justify-center z-50"
            @click.self="closeLightbox()" @keyup.escape.window="closeLightbox()">
            <div class="relative max-w-5xl w-[90%] bg-white rounded-xl shadow-2xl p-4">
                <button type="button"
                    class="absolute -top-3 -right-3 h-10 w-10 rounded-full bg-white shadow-lg border font-bold"
                    style="color:#1F4E79;border-color:#D7DDE5;" @click="closeLightbox()">
                    ×
                </button>
                <div class="relative">
                    <img :src="lightboxList[lightboxIndex]?.url" alt="Preview"
                        class="w-full max-h-[80vh] object-contain rounded-lg">
                    <div class="absolute left-3 bottom-3 px-3 py-1 rounded-md text-xs font-semibold"
                        style="background:rgba(0,0,0,0.65);color:#FFFFFF;">
                        <span x-text="lightboxList[lightboxIndex]?.label
                                ? ('Uploaded at ' + lightboxList[lightboxIndex].label)
                                : 'Uploaded at N/A'"></span>
                    </div>
                    <template x-if="lightboxList.length > 1">
                        <button type="button"
                            class="absolute left-0 top-1/2 -translate-y-1/2 h-12 w-12 rounded-full bg-white/90 border font-bold shadow-lg"
                            style="color:#1F4E79;border-color:#D7DDE5;" @click.stop="prevLightbox()">
                            ‹
                        </button>
                    </template>
                    <template x-if="lightboxList.length > 1">
                        <button type="button"
                            class="absolute right-0 top-1/2 -translate-y-1/2 h-12 w-12 rounded-full bg-white/90 border font-bold shadow-lg"
                            style="color:#1F4E79;border-color:#D7DDE5;" @click.stop="nextLightbox()">
                            ›
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </section>
    <script>
        function taskDetailPage(jobId) {
            return {
                jobId,
                job: {},
                beforePhoto: null,
                afterPhotos: [],
                currentAfterIndex: 0,
                showLightbox: false,
                lightboxList: [],
                lightboxIndex: 0,

                // ====== NEW: local preview (before submit) ======
                proofPreviewUrls: [],
                renderProofPreview(e) {
                    const files = e.target.files || [];

                    // revoke old urls to avoid memory leak
                    this.proofPreviewUrls.forEach(u => URL.revokeObjectURL(u.url));
                    this.proofPreviewUrls = [];

                    Array.from(files).forEach(file => {
                        if (!file.type.startsWith('image/')) return;
                        this.proofPreviewUrls.push({
                            url: URL.createObjectURL(file),
                            uploaded_at: null,
                            label: 'Local preview',
                        });
                    });
                },

                get urgencyChip() {
                    const u = (this.job.urgency || 'Medium').toLowerCase();
                    if (u === 'high') return { label: 'High urgency', bg: '#E74C3C', fg: '#FFFFFF' };
                    if (u === 'low') return { label: 'Low urgency', bg: '#2ECC71', fg: '#FFFFFF' };
                    return { label: 'Medium urgency', bg: '#F1C40F', fg: '#000000' };
                },
                get statusChip() {
                    const s = this.job.status || 'Assigned';
                    if (s === 'In_Progress') return { label: 'In Progress', bg: '#3498DB', fg: '#FFFFFF' };
                    if (s === 'Completed') return { label: 'Completed', bg: '#27AE60', fg: '#FFFFFF' };
                    return { label: s, bg: '#F39C12', fg: '#FFFFFF' };
                },
                chipStyle(bg, fg) { return `background-color:${bg};color:${fg};`; },

                nextAfter() {
                    this.currentAfterIndex = (this.currentAfterIndex + 1) % this.afterPhotos.length;
                },
                prevAfter() {
                    this.currentAfterIndex = (this.currentAfterIndex - 1 + this.afterPhotos.length) % this.afterPhotos.length;
                },
                openLightbox(list, index = 0) {
                    const arr = Array.isArray(list) ? list.filter(Boolean) : [];
                    if (!arr.length) return;
                    // normalize to objects with url + label
                    this.lightboxList = arr.map(item => {
                        if (typeof item === 'string') return { url: item, label: '' };
                        return {
                            url: item.url || '',
                            label: item.uploaded_at
                                ? new Date(item.uploaded_at).toLocaleString()
                                : (item.label || ''),
                        };
                    }).filter(i => i.url);
                    this.lightboxIndex = Math.min(Math.max(index, 0), this.lightboxList.length - 1);
                    this.showLightbox = true;
                },
                nextLightbox() {
                    if (!this.lightboxList.length) return;
                    this.lightboxIndex = (this.lightboxIndex + 1) % this.lightboxList.length;
                },
                prevLightbox() {
                    if (!this.lightboxList.length) return;
                    this.lightboxIndex = (this.lightboxIndex - 1 + this.lightboxList.length) % this.lightboxList.length;
                },
                closeLightbox() {
                    this.showLightbox = false;
                    this.lightboxList = [];
                    this.lightboxIndex = 0;
                },

                async load() {
                    try {
                        const res = await fetch(`/api/tech/tasks/${this.jobId}`, { credentials: 'same-origin' });
                        if (!res.ok) throw new Error('Failed to load task');
                        const json = await res.json();
                        const data = json.data || {};

                        const fmt = (iso) => {
                            if (!iso) return { date: '-', time: '' };
                            const d = new Date(iso);
                            if (isNaN(d)) return { date: '-', time: '' };
                            return {
                                date: d.toLocaleDateString(),
                                time: d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                                full: d,
                                display: d.toLocaleString(),
                            };
                        };
                        const reported = fmt(data.reported_at);
                        const due = fmt(data.due_at);
                        const completed = fmt(data.completed_at);
                        const now = new Date();

                        let isOverdue = false;
                        let overdueLabel = 'On track';
                        if (due.full) {
                            if (data.status_value !== 'Completed' && now > due.full) {
                                isOverdue = true;
                                const diffMs = now - due.full;
                                const h = Math.floor(diffMs / 3600000);
                                const m = Math.floor((diffMs % 3600000) / 60000);
                                const label = h > 0 ? `${h}h ${m}m` : `${m}m`;
                                overdueLabel = `Overdue ${label}`;
                            } else if (completed.full && completed.full > due.full) {
                                isOverdue = true;
                                const diffMs = completed.full - due.full;
                                const h = Math.floor(diffMs / 3600000);
                                const m = Math.floor((diffMs % 3600000) / 60000);
                                const label = h > 0 ? `${h}h ${m}m` : `${m}m`;
                                overdueLabel = `Completed late ${label}`;
                            }
                        }

                        this.job = {
                            id: data.id,
                            description: data.description,
                            urgency: data.urgency,
                            status: data.status_value, // must match 'In_Progress' / 'Completed' / etc.
                            reported_at: reported.display || '-',
                            reported_date: reported.date,
                            reported_time: reported.time,
                            due_at: due.display || '-',
                            due_date: due.date,
                            due_time: due.time,
                            completed_at: completed.display || null,
                            completed_date: completed.date,
                            completed_time: completed.time,
                            is_overdue: isOverdue,
                            overdue_label: overdueLabel,
                            location: [data.location?.campus, data.location?.block, data.location?.room].filter(Boolean).join(', '),
                            category: data.category,
                        };

                        this.beforePhoto = data.attachments?.reporter_proof || null;
                        this.afterPhotos = (data.attachments?.technician_proofs || []).map(p => ({
                            id: p.id ?? 0,
                            url: p.url,
                            uploaded_at: p.uploaded_at || null,
                        }));
                        this.currentAfterIndex = 0;

                        // reset local preview when loading job
                        this.proofPreviewUrls.forEach(u => URL.revokeObjectURL(u));
                        this.proofPreviewUrls = [];
                    } catch (e) {
                        console.error(e);
                    }
                },
            };
        }
    </script>
@endsection

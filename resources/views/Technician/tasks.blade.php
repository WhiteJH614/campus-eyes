@extends('layouts.app')

@php
    $pageTitle = 'Assigned Jobs';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'Technician Dashboard', 'url' => route('technician.dashboard')],
        ['label' => 'Assigned Jobs'],
    ];
@endphp

@section('content')
    <section class="space-y-6" x-data="tasksPage()" x-init="load()">
        <div class="rounded-2xl shadow-sm border border-[#D7DDE5] bg-white p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-[#2C3E50]">Campus Maps</h1>
                    <p class="text-sm text-[#7F8C8D]">
                        TAR UMT Penang Branch - 3D Campus View
                    </p>
                </div>
            </div>

            {{-- 3D Campus View with Three.js --}}
            <div class="mt-4 grid gap-4 lg:grid-cols-3 items-stretch">
                <div
                    class="lg:col-span-2 rounded-xl border border-[#D7DDE5] bg-gradient-to-br from-[#F7FBFF] via-white to-[#EFF4FF] p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <p class="text-xs uppercase tracking-[0.1em] text-[#7F8C8D]">3D Route Assist</p>
                            <h2 class="text-lg font-semibold text-[#1F4E79]">Interactive Campus View</h2>
                        </div>
                        <span class="text-[11px] font-semibold text-[#1E8449] bg-[#E8F8F0] px-3 py-1 rounded-full">
                            Click buildings to view tasks
                        </span>
                    </div>

                    {{-- Three.js 3D Canvas --}}
                    <div class="relative rounded-xl overflow-hidden border-2 border-[#90caf9] shadow-lg"
                        style="height: 500px;">
                        <canvas id="campusCanvas" class="w-full h-full"></canvas>

                        {{-- Loading indicator --}}
                        <div x-show="!sceneReady" class="absolute inset-0 flex items-center justify-center bg-white/80">
                            <div class="text-center">
                                <svg class="animate-spin h-10 w-10 text-[#1F4E79] mx-auto mb-2"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <p class="text-sm text-[#7F8C8D]">Loading 3D Campus...</p>
                            </div>
                        </div>

                        {{-- Fallback if Three.js is unavailable (e.g., offline or blocked) --}}
                        <div x-show="threeUnavailable"
                            class="absolute inset-0 flex items-center justify-center bg-white/90 backdrop-blur-sm text-center px-6">
                            <div>
                                <div class="text-lg font-semibold text-[#1F4E79] mb-2">3D view unavailable</div>
                                <p class="text-sm text-[#7F8C8D]">Three.js failed to load (offline or blocked). Core tasks
                                    and table remain available.</p>
                            </div>
                        </div>

                        {{-- Controls info --}}
                        <div
                            class="absolute bottom-3 left-3 bg-white/90 backdrop-blur-sm rounded-lg px-3 py-2 text-xs text-[#2C3E50] shadow-lg">
                            <div class="font-semibold mb-1">Controls:</div>
                            <div>🖱️ Left Click: Rotate</div>
                            <div>🖱️ Right Click: Pan</div>
                            <div>⚙️ Scroll: Zoom</div>
                            <div>🏢 Click Building: Select</div>
                        </div>

                        {{-- Selected building info --}}
                        <div x-show="focusArea"
                            class="absolute top-3 left-3 bg-white/95 backdrop-blur-sm rounded-xl px-4 py-3 shadow-xl border-2 border-yellow-400 max-w-xs">
                            <div class="text-xs text-[#7F8C8D] mb-1">Selected Building</div>
                            <div class="text-lg font-bold text-[#1F4E79]" x-text="focusArea"></div>
                            <div class="mt-2 flex items-center gap-2">
                                <span class="px-2 py-1 rounded-full text-xs font-bold bg-blue-500 text-white"
                                    x-text="selectedTaskCount + ' tasks'"></span>
                                <span x-show="nearbyBlocks.length > 0" class="text-xs text-[#7F8C8D]"
                                    x-text="nearbyBlocks.length + ' nearby'"></span>
                            </div>
                        </div>

                        {{-- Live tasks preview (keeps updates visible while viewing the map) --}}
                        <div x-show="focusArea"
                            class="absolute bottom-3 right-3 w-64 max-w-[70%] bg-white/95 backdrop-blur-sm rounded-xl px-3 py-3 shadow-xl border border-[#D7DDE5] pointer-events-auto">
                            <div class="flex items-center justify-between gap-2">
                                <div>
                                    <div class="text-[10px] uppercase tracking-[0.12em] text-[#7F8C8D]">Live tasks</div>
                                    <div class="text-sm font-semibold text-[#1F4E79]" x-text="focusArea"></div>
                                </div>
                                <a href="#building-tasks-panel" class="text-[11px] text-[#1F4E79] hover:underline">
                                    Open panel ->
                                </a>
                            </div>
                            <div class="mt-2 space-y-2 max-h-32 overflow-auto">
                                <template x-if="focusTasks.length === 0">
                                    <div class="text-xs text-[#7F8C8D]">No tasks in this building.</div>
                                </template>
                                <template x-for="task in focusTasks.slice(0, 3)" :key="task.id">
                                    <div class="rounded-lg border border-[#E5EAEE] bg-white/80 p-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-semibold text-[#1F4E79]" x-text="'#'+task.id"></span>
                                            <span class="text-[10px] px-2 py-0.5 rounded-full"
                                                :style="statusStyle(task.status)">
                                                <span x-text="task.status.replace('_',' ')"></span>
                                            </span>
                                        </div>
                                        <div class="text-[11px] text-[#7F8C8D] mt-1">
                                            <span x-text="task.room_name || task.block_name || 'N/A'"></span>
                                        </div>
                                        <div class="text-[11px] text-[#2C3E50] mt-1">
                                            <span class="font-semibold">Due:</span>
                                            <span x-text="task.due_at || 'N/A'"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <div class="mt-2 text-[11px] text-[#7F8C8D]" x-show="focusTasks.length > 3">
                                Showing 3 of <span x-text="focusTasks.length"></span>
                            </div>
                        </div>

                        {{-- Reset view button --}}
                        <button type="button" @click="resetCamera()"
                            class="absolute top-3 right-3 w-10 h-10 rounded-lg bg-white border-2 border-[#1F4E79] shadow-lg hover:shadow-xl hover:bg-blue-50 flex items-center justify-center font-bold text-[#1F4E79] transition-all"
                            title="Reset Camera">
                            ⟲
                        </button>
                    </div>

                    {{-- Legend --}}
                    <div class="mt-4 flex items-center gap-4 text-xs flex-wrap">
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 rounded bg-yellow-400 border-2 border-yellow-600 shadow-sm"></div>
                            <span class="text-[#2C3E50] font-medium">Selected</span>
                        </div>
                        <!-- <div class="flex items-center gap-2">
                                                <div class="w-5 h-5 rounded bg-blue-400 border-2 border-blue-600 shadow-sm"></div>
                                                <span class="text-[#2C3E50] font-medium">Nearby zone</span>
                                            </div> -->
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 rounded bg-gray-400 border-2 border-gray-600 shadow-sm"></div>
                            <span class="text-[#2C3E50] font-medium">Other blocks</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 rounded-full bg-red-500 shadow-sm"></div>
                            <span class="text-[#2C3E50] font-medium">Has tasks</span>
                        </div>
                    </div>
                </div>

                {{-- Tasks panel --}}
                <div id="building-tasks-panel"
                    class="rounded-xl border border-[#D7DDE5] bg-white p-4 flex flex-col lg:sticky lg:top-24 self-start">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-[0.1em] text-[#7F8C8D]">Building Tasks</p>
                            <h3 class="text-lg font-semibold text-[#2C3E50]" x-text="focusArea || 'Click a building'"></h3>
                            <p class="text-[11px] text-[#7F8C8D] mt-1" x-show="nearbyBlocks.length > 0">
                                <span x-text="nearbyBlocks.length"></span> nearby block<span
                                    x-show="nearbyBlocks.length !== 1">s</span>
                            </p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span
                                class="text-[11px] px-2 py-1 rounded-full bg-yellow-100 text-yellow-800 font-semibold border border-yellow-200">
                                🟡 This Block
                            </span>
                            <span
                                class="text-[11px] px-2 py-1 rounded-full bg-blue-100 text-blue-800 font-semibold border border-blue-200"
                                x-show="nearbyBlocks.length > 0">
                                🔵 Nearby
                            </span>
                        </div>
                    </div>

                    <div class="mt-3 space-y-2 overflow-auto max-h-96">
                        <template x-if="focusTasks.length === 0">
                            <div class="text-center py-8">
                                <p class="text-sm text-[#7F8C8D]" x-show="!focusArea">Select a building to view tasks</p>
                                <p class="text-sm text-[#7F8C8D]" x-show="focusArea">No tasks in this area</p>
                            </div>
                        </template>
                        <template x-for="task in focusTasks" :key="task.id">
                            <a :href="`/technician/tasks/${task.id}`"
                                class="block rounded-lg border-2 p-3 hover:shadow-md transition-all duration-200" :class="{
                                                                    'border-yellow-400 bg-yellow-50': isSelectedBlock(task),
                                                                    'border-blue-300 bg-blue-50': isNearbyBlock(task)
                                                                }">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-base" x-text="isSelectedBlock(task) ? '🟡' : '🔵'"></span>
                                        <div class="text-sm font-semibold text-[#1F4E79]" x-text="'#'+task.id"></div>
                                    </div>
                                    <span class="text-[11px] px-2 py-1 rounded-full" :style="statusStyle(task.status)">
                                        <span class="font-semibold" x-text="task.status.replace('_',' ')"></span>
                                    </span>
                                </div>
                                <div class="text-xs text-[#7F8C8D] mt-1">
                                    <span class="font-semibold" x-text="task.block_name"></span>
                                    · <span x-text="task.room_name || 'N/A'"></span>
                                </div>
                                <div class="text-xs text-[#2C3E50] mt-1">
                                    <span class="font-semibold">Category:</span>
                                    <span x-text="task.category || 'General'"></span>
                                </div>
                                <div class="text-xs text-[#2C3E50] mt-1">
                                    <span class="font-semibold">Due:</span>
                                    <span x-text="task.due_at || 'N/A'"></span>
                                </div>
                            </a>
                        </template>
                    </div>

                    <!-- <div class="mt-3 pt-3 border-t border-[#D7DDE5]" x-show="focusArea">
                                        <div class="text-xs text-[#7F8C8D] bg-[#F0F9FF] p-2 rounded-lg border border-[#BFDBFE]">
                                            <span class="font-semibold text-[#1F4E79]">💡 Tip:</span>
                                            Complete tasks in <span class="text-[#1F4E79] font-semibold" x-text="focusArea"></span> first,
                                            then move to nearby blocks.
                                        </div>
                                    </div> -->
                </div>
            </div>


        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-[#D7DDE5] p-6">
            <div>
                <h1 class="text-2xl font-semibold text-[#2C3E50]">Assigned Jobs</h1>
                <p class="text-sm text-[#7F8C8D]">
                    TAR UMT Penang Branch - List of your assigned maintenance jobs
                </p>
            </div>
            {{-- Filters --}}
            <form class="mt-4 grid gap-3 lg:grid-cols-4" @submit.prevent="load(1)">
                <input type="text" x-model="filters.q" placeholder="Search by Report ID" @input.debounce.400ms="load(1)"
                    class="rounded-lg px-3 py-2 border border-[#D7DDE5] bg-white text-[#2C3E50] focus:ring-2 focus:ring-[#1F4E79] focus:border-transparent" />

                <select x-model="filters.status" @change="load(1)"
                    class="rounded-lg px-3 py-2 border border-[#D7DDE5] bg-white text-[#2C3E50] focus:ring-2 focus:ring-[#1F4E79] focus:border-transparent">
                    <option value="">All Status</option>
                    <template x-for="s in ['Assigned','In_Progress','Overdue']" :key="s">
                        <option :value="s" x-text="s.replace('_',' ')"></option>
                    </template>
                </select>

                <select x-model="filters.urgency" @change="load(1)"
                    class="rounded-lg px-3 py-2 border border-[#D7DDE5] bg-white text-[#2C3E50] focus:ring-2 focus:ring-[#1F4E79] focus:border-transparent">
                    <option value="">All Urgency</option>
                    <template x-for="u in ['High','Medium','Low']" :key="u">
                        <option :value="u" x-text="u"></option>
                    </template>
                </select>

                <select x-model="filters.sort" @change="load(1)"
                    class="rounded-lg px-3 py-2 border border-[#D7DDE5] bg-white text-[#2C3E50] focus:ring-2 focus:ring-[#1F4E79] focus:border-transparent">
                    <option value="due">Sort: Due date</option>
                    <option value="urgency">Sort: Urgency</option>
                    <option value="block">Sort: Block</option>
                </select>
            </form>

            {{-- Jobs table --}}
            <div class="overflow-x-auto mt-4">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-[#F5F7FA] text-[#2C3E50]">
                            <th class="text-left px-3 py-2 font-semibold">Report ID</th>
                            <th class="text-left px-3 py-2 font-semibold">Date reported</th>
                            <th class="text-left px-3 py-2 font-semibold">Block</th>
                            <th class="text-left px-3 py-2 font-semibold">Room</th>
                            <th class="text-left px-3 py-2 font-semibold">Category</th>
                            <th class="text-left px-3 py-2 font-semibold">Urgency</th>
                            <th class="text-left px-3 py-2 font-semibold">Status</th>
                            <th class="text-left px-3 py-2 font-semibold">Due date</th>
                            <th class="text-left px-3 py-2 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-[#2C3E50]">
                        <template x-if="loading">
                            <tr>
                                <td colspan="9" class="px-3 py-4 text-center text-[#7F8C8D]">
                                    <div class="flex items-center justify-center gap-2">
                                        <svg class="animate-spin h-5 w-5 text-[#1F4E79]" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        <span>Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="!loading && jobs.length === 0">
                            <tr>
                                <td colspan="9" class="px-3 py-4 text-center text-[#7F8C8D]">No assigned jobs found.</td>
                            </tr>
                        </template>
                        <template x-for="job in jobs" :key="job.id">
                            <tr class="border-t border-[#D7DDE5] hover:bg-[#F9FBFF] transition">
                                <td class="px-3 py-2 font-semibold text-[#1F4E79]">
                                    <a :href="`/technician/tasks/${job.id}`" class="hover:underline" x-text="''+job.id"></a>
                                </td>
                                <td class="px-3 py-2" x-text="job.reported_at"></td>
                                <td class="px-3 py-2 font-semibold" x-text="job.block_name || '-'"></td>
                                <td class="px-3 py-2" x-text="job.room_name || '-'"></td>
                                <td class="px-3 py-2" x-text="job.category"></td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold"
                                        :style="urgencyStyle(job.urgency)">
                                        <span x-text="job.urgency || 'N/A'"></span>
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold"
                                        :style="statusStyle(job.status)">
                                        <span x-text="job.status.replace('_',' ')"></span>
                                    </span>
                                </td>
                                <td class="px-3 py-2" x-text="job.due_at"></td>
                                <td class="px-3 py-2">
                                    <a :href="`/technician/tasks/${job.id}`"
                                        class="text-xs font-semibold text-[#1F4E79] hover:underline">
                                        View
                                    </a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <div class="mt-4 flex items-center gap-3 justify-end" x-show="pagination.total > 0">
                    <button
                        class="px-3 py-1 rounded border border-[#D7DDE5] text-sm hover:bg-[#F5F7FA] transition disabled:opacity-50"
                        :disabled="pagination.current_page <= 1" @click="changePage(pagination.current_page - 1)">
                        ← Prev
                    </button>
                    <div class="text-sm text-[#2C3E50]">
                        Page <span class="font-semibold" x-text="pagination.current_page"></span> of <span
                            class="font-semibold" x-text="pagination.last_page"></span>
                    </div>
                    <button
                        class="px-3 py-1 rounded border border-[#D7DDE5] text-sm hover:bg-[#F5F7FA] transition disabled:opacity-50"
                        :disabled="pagination.current_page >= pagination.last_page"
                        @click="changePage(pagination.current_page + 1)">
                        Next →
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- Three.js (module) loader, matching the report create view --}}
    <script type="importmap">
                            {
                                "imports": {
                                    "three": "https://unpkg.com/three@0.160.0/build/three.module.js",
                                    "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/"
                                }
                            }
                        </script>
    <script type="module">
        import * as THREE from 'three';
        import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
        import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';

        window.THREE = THREE;
        window.OrbitControls = OrbitControls;
        window.GLTFLoader = GLTFLoader;
        window.dispatchEvent(new Event('three-ready'));
    </script>

    <script>
        function tasksPage() {
            return {
                jobs: [],
                blocks: [],
                pagination: { current_page: 1, last_page: 1, total: 0 },
                filters: { q: '', status: '', urgency: '', sort: 'due' },
                campusModelUrl: '{{ asset("campus.glb") }}',
                blockMapping: {
                    Admin: 'Admin',
                    BlockA: 'Block A',
                    BlockB: 'Block B',
                    BlockC: 'Block C',
                    BlockD: 'Block D',
                    BlockE: 'Block E',
                    BlockF: 'Block F',
                    BlockG: 'Block G',
                    BlockH: 'Block H',
                    BlockJ: 'Block J',
                    BlockK: 'Block K',
                    BlockM: 'Block M',
                    Canteen: 'Canteen',
                    DKBuilding1: 'DK Building 1',
                    DKBuilding2: 'DK Building 2',
                    Hall: 'Hall',
                    IDK: 'IDK',
                    Library: 'Library',
                    Multipurpose1: 'Multipurpose 1',
                    Multipurpose2: 'Multipurpose 2',
                },
                focusArea: '',
                focusTasks: [],
                selectedBlock: null,
                nearbyBlocks: [],
                selectedTaskCount: 0,
                loading: false,
                sceneReady: false,
                threeUnavailable: false,

                // Three.js objects are stored on the canvas to avoid Alpine proxy issues.
                getThreeStore() {
                    const canvas = document.getElementById('campusCanvas');
                    if (!canvas) return null;
                    if (!canvas.__threeStore) {
                        canvas.__threeStore = {
                            scene: null,
                            camera: null,
                            renderer: null,
                            controls: null,
                            raycaster: null,
                            mouse: null,
                            clickableObjects: [],
                            hoveredObject: null,
                            blockMeshes: {},
                        };
                    }
                    return canvas.__threeStore;
                },

                async waitForThree(timeoutMs = 5000) {
                    if (window.THREE && window.OrbitControls && window.GLTFLoader) return true;
                    return new Promise((resolve) => {
                        const onReady = () => {
                            cleanup();
                            resolve(true);
                        };
                        const cleanup = () => {
                            clearTimeout(timer);
                            window.removeEventListener('three-ready', onReady);
                        };
                        const timer = setTimeout(() => {
                            cleanup();
                            resolve(false);
                        }, timeoutMs);
                        window.addEventListener('three-ready', onReady, { once: true });
                    });
                },

                urgencyStyle(u) {
                    const map = {
                        high: ['#E74C3C', '#FFFFFF'],
                        medium: ['#F1C40F', '#2C3E50'],
                        low: ['#2ECC71', '#FFFFFF'],
                    };
                    const key = (u || '').toLowerCase();
                    const [bg, fg] = map[key] || ['#D7DDE5', '#2C3E50'];
                    return `background-color:${bg};color:${fg};`;
                },

                statusStyle(s) {
                    const map = {
                        Pending: ['#95A5A6', '#FFFFFF'],
                        Assigned: ['#F39C12', '#FFFFFF'],
                        In_Progress: ['#3498DB', '#FFFFFF'],
                        Completed: ['#27AE60', '#FFFFFF'],
                        Overdue: ['#E74C3C', '#FFFFFF'],
                    };
                    const [bg, fg] = map[s] || ['#D7DDE5', '#2C3E50'];
                    return `background-color:${bg};color:${fg};`;
                },

                buildUrl(page = 1) {
                    const params = new URLSearchParams();
                    if (this.filters.q) params.append('q', this.filters.q);
                    if (this.filters.status) params.append('status', this.filters.status);
                    if (this.filters.urgency) params.append('urgency', this.filters.urgency);
                    if (this.filters.sort) params.append('sort', this.filters.sort);
                    params.append('page', page);
                    return `/api/tech/tasks?${params.toString()}`;
                },

                async load(page = 1) {
                    this.loading = true;
                    try {
                        const res = await fetch(this.buildUrl(page), { credentials: 'same-origin' });
                        if (!res.ok) throw new Error('Failed to load');
                        const json = await res.json();
                        const data = json.data || {};

                        this.jobs = (data.jobs || []);
                        this.blocks = data.blocks || []; // Assume API returns blocks
                        this.pagination = data.pagination || { current_page: 1, last_page: 1, total: 0 };

                        if (!this.sceneReady) {
                            await this.init3DScene();
                        } else {
                            this.update3DScene();
                        }
                    } catch (e) {
                        console.error(e);
                        this.jobs = [];
                    } finally {
                        this.loading = false;
                    }
                },

                async init3DScene() {
                    const canvas = document.getElementById('campusCanvas');
                    if (!canvas) return;
                    const store = this.getThreeStore();
                    if (!store) return;
                    this.threeUnavailable = false;
                    const ready = await this.waitForThree();
                    if (!ready) {
                        console.error('Three.js failed to load; 3D view unavailable.');
                        this.threeUnavailable = true;
                        this.sceneReady = true; // stop loader
                        return;
                    }
                    const THREERef = window.THREE;

                    // Scene setup
                    store.scene = new THREERef.Scene();
                    store.scene.background = new THREERef.Color(0x87CEEB);
                    store.scene.fog = new THREERef.Fog(0x87CEEB, 200, 500);

                    // Camera
                    store.camera = new THREERef.PerspectiveCamera(
                        75,
                        canvas.clientWidth / canvas.clientHeight,
                        0.1,
                        1000
                    );
                    store.camera.position.set(0, 150, -180);

                    // Renderer
                    store.renderer = new THREERef.WebGLRenderer({ canvas, antialias: true });
                    store.renderer.setSize(canvas.clientWidth, canvas.clientHeight);
                    store.renderer.shadowMap.enabled = true;
                    store.renderer.shadowMap.type = THREERef.PCFSoftShadowMap;
                    store.renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));

                    // Controls
                    store.controls = new window.OrbitControls(store.camera, canvas);
                    store.controls.enableDamping = true;
                    store.controls.minPolarAngle = 0.1;
                    store.controls.maxPolarAngle = Math.PI / 2 - 0.05;
                    store.controls.minDistance = 20;
                    store.controls.maxDistance = 400;

                    // Raycaster for click detection
                    store.raycaster = new THREERef.Raycaster();
                    store.mouse = new THREERef.Vector2();

                    // Lights
                    const hemiLight = new THREERef.HemisphereLight(0xffffff, 0x444444, 0.6);
                    hemiLight.position.set(0, 200, 0);
                    store.scene.add(hemiLight);

                    const sunLight = new THREERef.DirectionalLight(0xffdfba, 1);
                    sunLight.position.set(50, 100, 50);
                    sunLight.castShadow = true;
                    sunLight.shadow.mapSize.width = 2048;
                    sunLight.shadow.mapSize.height = 2048;
                    store.scene.add(sunLight);

                    // Ground
                    const groundGeo = new THREERef.PlaneGeometry(350, 300);
                    const groundMat = new THREERef.MeshStandardMaterial({ color: 0x4caf50, roughness: 0.8, metalness: 0.2 });
                    const ground = new THREERef.Mesh(groundGeo, groundMat);
                    ground.rotation.x = -Math.PI / 2;
                    ground.receiveShadow = true;
                    ground.position.y = -0.1;
                    store.scene.add(ground);

                    const modelLoaded = await this.loadCampusModel();
                    if (!modelLoaded) {
                        this.threeUnavailable = true;
                        this.sceneReady = true;
                        return;
                    }

                    // Click event
                    canvas.addEventListener('click', (e) => this.onCanvasClick(e));
                    canvas.addEventListener('mousemove', (e) => this.onCanvasHover(e));

                    // Handle resize
                    window.addEventListener('resize', () => this.onWindowResize());

                    // Animation loop
                    this.animate();

                    this.sceneReady = true;
                },

                normalizeBlockName(name) {
                    return (name || '').toString().trim().toUpperCase();
                },

                getBlockNameFromMesh(meshName) {
                    return this.blockMapping[meshName] || meshName;
                },

                setMaterialColor(mesh, hexColor) {
                    if (Array.isArray(mesh.material)) {
                        mesh.material.forEach(mat => mat.color && mat.color.setHex(hexColor));
                        return;
                    }
                    if (mesh.material?.color) mesh.material.color.setHex(hexColor);
                },

                setMaterialEmissive(mesh, hexColor, intensity = 0) {
                    const apply = (mat) => {
                        if (mat?.emissive) {
                            mat.emissive.setHex(hexColor);
                            mat.emissiveIntensity = intensity;
                        }
                    };
                    if (Array.isArray(mesh.material)) {
                        mesh.material.forEach(apply);
                        return;
                    }
                    apply(mesh.material);
                },

                applyMeshState(mesh, isHover = false) {
                    const blockName = mesh.userData.blockName;
                    const taskCount = mesh.userData.taskCount || 0;
                    const isSelected = this.normalizeBlockName(this.focusArea) === this.normalizeBlockName(blockName);
                    const baseColor = mesh.userData.originalColor ?? 0x808080;

                    if (isSelected) {
                        this.setMaterialColor(mesh, 0xffd700);
                        this.setMaterialEmissive(mesh, 0xffaa00, 0.5);
                        return;
                    }

                    if (isHover) {
                        this.setMaterialColor(mesh, 0xffd700);
                        this.setMaterialEmissive(mesh, 0x332200, 0.2);
                        return;
                    }

                    this.setMaterialColor(mesh, baseColor);
                    if (taskCount > 0) {
                        this.setMaterialEmissive(mesh, 0xff0000, 0.35);
                    } else {
                        this.setMaterialEmissive(mesh, 0x000000, 0);
                    }
                },

                async loadCampusModel() {
                    const store = this.getThreeStore();
                    if (!store || !store.scene) return false;

                    store.clickableObjects = [];
                    store.blockMeshes = {};

                    const loader = new window.GLTFLoader();
                    const targetNames = Object.keys(this.blockMapping);

                    return new Promise((resolve) => {
                        loader.load(this.campusModelUrl, (gltf) => {
                            const model = gltf.scene;
                            store.scene.add(model);

                            model.traverse((child) => {
                                if (!child.isMesh) return;
                                child.castShadow = true;
                                child.receiveShadow = true;

                                if (targetNames.includes(child.name)) {
                                    if (Array.isArray(child.material)) {
                                        child.material = child.material.map(mat => mat.clone());
                                    } else {
                                        child.material = child.material.clone();
                                    }

                                    const blockName = this.getBlockNameFromMesh(child.name);
                                    child.userData.originalColor = Array.isArray(child.material)
                                        ? child.material[0]?.color?.getHex?.()
                                        : child.material?.color?.getHex?.();
                                    child.userData.blockName = blockName;

                                    store.clickableObjects.push(child);
                                    store.blockMeshes[blockName] = child;
                                }
                            });

                            this.update3DScene();
                            resolve(true);
                        }, undefined, (error) => {
                            console.error('Error loading 3D map', error);
                            resolve(false);
                        });
                    });
                },

                onCanvasHover(event) {
                    const canvas = document.getElementById('campusCanvas');
                    const store = this.getThreeStore();
                    if (!canvas || !store || store.clickableObjects.length === 0) return;

                    const rect = canvas.getBoundingClientRect();
                    store.mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
                    store.mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

                    store.raycaster.setFromCamera(store.mouse, store.camera);
                    const intersects = store.raycaster.intersectObjects(store.clickableObjects);

                    if (intersects.length > 0) {
                        const hitObj = intersects[0].object;
                        if (store.hoveredObject !== hitObj) {
                            if (store.hoveredObject) this.applyMeshState(store.hoveredObject);
                            store.hoveredObject = hitObj;
                            this.applyMeshState(hitObj, true);
                            document.body.style.cursor = 'pointer';
                        }
                        return;
                    }

                    if (store.hoveredObject) {
                        this.applyMeshState(store.hoveredObject);
                        store.hoveredObject = null;
                    }
                    document.body.style.cursor = 'default';
                },

                update3DScene() {
                    const store = this.getThreeStore();
                    if (!store || store.clickableObjects.length === 0) return;

                    const counts = this.jobs.reduce((acc, job) => {
                        const key = this.normalizeBlockName(job.block_name);
                        acc[key] = (acc[key] || 0) + 1;
                        return acc;
                    }, {});

                    Object.keys(store.blockMeshes).forEach(blockName => {
                        const mesh = store.blockMeshes[blockName];
                        const countKey = this.normalizeBlockName(blockName);
                        mesh.userData.taskCount = counts[countKey] || 0;
                        this.applyMeshState(mesh);
                    });
                },

                onCanvasClick(event) {
                    const canvas = document.getElementById('campusCanvas');
                    const rect = canvas.getBoundingClientRect();
                    const store = this.getThreeStore();
                    if (!store) return;

                    if (store.hoveredObject?.userData?.blockName) {
                        this.setFocusArea(store.hoveredObject.userData.blockName);
                        this.update3DScene();
                        return;
                    }

                    store.mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
                    store.mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

                    store.raycaster.setFromCamera(store.mouse, store.camera);
                    const intersects = store.raycaster.intersectObjects(store.clickableObjects);

                    if (intersects.length > 0) {
                        const selected = intersects[0].object;
                        this.setFocusArea(selected.userData.blockName);
                        this.update3DScene();
                    }
                },

                setFocusArea(blockName) {
                    this.focusArea = blockName;
                    this.selectedBlock = blockName;

                    const tasks = this.jobs.filter(j =>
                        this.normalizeBlockName(j.block_name) === this.normalizeBlockName(blockName)
                    );
                    this.selectedTaskCount = tasks.length;
                    this.focusTasks = tasks.slice(0, 10);

                    // Update colors
                    const store = this.getThreeStore();
                    if (!store) return;
                    Object.keys(store.blockMeshes).forEach(name => {
                        const mesh = store.blockMeshes[name];
                        this.applyMeshState(mesh);
                    });

                    const panel = document.getElementById('building-tasks-panel');
                    if (panel) {
                        panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                },

                resetCamera() {
                    const store = this.getThreeStore();
                    if (!store) return;
                    store.camera.position.set(0, 150, -180);
                    store.camera.lookAt(0, 0, 0);
                    store.controls.reset();
                },

                animate() {
                    requestAnimationFrame(() => this.animate());
                    const store = this.getThreeStore();
                    if (!store || !store.renderer) return;
                    store.controls.update();
                    store.renderer.render(store.scene, store.camera);
                },

                onWindowResize() {
                    const canvas = document.getElementById('campusCanvas');
                    const store = this.getThreeStore();
                    if (!canvas || !store) return;
                    store.camera.aspect = canvas.clientWidth / canvas.clientHeight;
                    store.camera.updateProjectionMatrix();
                    store.renderer.setSize(canvas.clientWidth, canvas.clientHeight);
                },

                changePage(p) {
                    if (p >= 1 && p <= this.pagination.last_page) this.load(p);
                },

                isSelectedBlock(task) {
                    return this.normalizeBlockName(task.block_name) === this.normalizeBlockName(this.focusArea);
                },

                isNearbyBlock(task) {
                    return false; // Implement proximity logic if needed
                },
            };
        }
    </script>
@endsection
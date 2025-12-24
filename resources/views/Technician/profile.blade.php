@extends('layouts.app')

@php
    $pageTitle = 'Technician Profile';
    $specializationOptions = [
        'Electrical' => 'Electrical',
        'Networking' => 'Networking',
        'AirConditioning' => 'Air Conditioning',
        'Plumbing' => 'Plumbing',
        'Carpentry' => 'Carpentry',
        'AudioVisual' => 'Audio / Visual',
        'Landscaping' => 'Landscaping',
        'Security' => 'Security Systems',
        'Cleaning' => 'Cleaning / Janitorial',
    ];
    $breadcrumbs = [
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'Technician Dashboard', 'url' => route('technician.dashboard')],
        ['label' => 'Profile'],
    ];
@endphp

@section('content')
    <section class="space-y-6" x-data="profilePage()" x-init="load()">
        <div class="rounded-2xl border border-transparent bg-gradient-to-r from-[#0F172A] via-[#1F4E79] to-[#2A7ABF] text-white p-6 shadow-md">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-full bg-white/20 flex items-center justify-center text-lg font-semibold">
                        <span x-text="avatarInitial"></span>
                    </div>
                    <div>
                        <div class="text-sm text-white/80">Technician profile</div>
                        <h1 class="text-2xl font-semibold" x-text="form.name || 'Technician'"></h1>
                        <p class="text-xs text-white/70 mt-1 flex items-center gap-2">
                            <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold" style="background:rgba(255,255,255,0.15);">
                                Technician
                            </span>
                            <span>Last updated: <span x-text="lastUpdated || '-'"></span></span>
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <div class="rounded-xl bg-white/15 px-4 py-3 min-w-[180px]">
                        <div class="text-xs text-white/80">Contact</div>
                        <div class="text-sm font-semibold" x-text="form.email"></div>
                        <template x-if="form.phone_number">
                            <div class="text-xs text-white/80" x-text="form.phone_number"></div>
                        </template>
                    </div>
                    <div class="rounded-xl bg-white/15 px-4 py-3 min-w-[160px]">
                        <div class="text-xs text-white/80">Campus</div>
                        <div class="text-sm font-semibold" x-text="form.campus || 'Not set'"></div>
                        <div class="text-xs text-white/70">Availability: <span x-text="availabilityText"></span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 rounded-2xl shadow-sm border border-[#D7DDE5] bg-white p-6">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-[#2C3E50]">Profile details</h2>
                        <p class="text-xs text-[#7F8C8D]">Update your contact info and skill areas.</p>
                    </div>
                    <span class="text-[11px] px-2 py-1 rounded-full" style="background:#F5F7FA;color:#1F4E79;">Tech</span>
                </div>
                <form class="space-y-4" @submit.prevent="saveProfile">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label for="name" class="text-sm font-medium text-[#2C3E50]">Full Name</label>
                            <input id="name" name="name" type="text" x-model="form.name" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" required>
                        </div>
                        <div class="space-y-2">
                            <label for="email" class="text-sm font-medium text-[#2C3E50]">Email</label>
                            <input id="email" type="email" x-model="form.email" readonly aria-readonly="true"
                                class="w-full rounded-lg px-3 py-2 border cursor-not-allowed"
                                style="border-color:#D7DDE5;color:#2C3E50;background:#F5F7FA;">
                        </div>
                    </div>

                    {{-- Row 2: Phone + Campus + Availability --}}
                    <div class="grid sm:grid-cols-3 gap-4">
                        <div class="space-y-2">
                            <label for="phone_number" class="text-sm font-medium text-[#2C3E50]">Phone</label>
                            <div class="flex rounded-lg overflow-hidden border border-[#D7DDE5] bg-white">
                                <span class="px-3 py-2 text-sm bg-[#F5F7FA] text-[#2C3E50] select-none border-r border-[#D7DDE5]">+60</span>
                                <input
                                    id="phone_number"
                                    name="phone_number_digits"
                                    type="tel"
                                    inputmode="numeric"
                                    pattern="[0-9]{9,11}"
                                    title="Enter 9-11 digits (a leading 0 will be removed automatically)"
                                    x-model="form.phone_number_digits"
                                    class="flex-1 px-3 py-2.5 text-sm focus:outline-none border-none"
                                    style="color:#2C3E50;background:transparent;"
                                >
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="campus" class="text-sm font-medium text-[#2C3E50]">Campus</label>
                            <select id="campus" name="campus" x-model="form.campus" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;">
                                <option value="">Select campus</option>
                                <option value="Penang">Penang</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="availability_status" class="text-sm font-medium text-[#2C3E50] flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#1F4E79]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 6v6l3 3" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="12" cy="12" r="9"/>
                                </svg>
                                Availability
                            </label>
                            <select
                                id="availability_status"
                                name="availability_status"
                                x-model="form.availability_status"
                                class="w-full rounded-lg px-3 py-2 border bg-white focus:outline-none focus:ring-2 focus:ring-[#1F4E79]/40 focus:border-[#1F4E79]"
                                style="border-color:#D7DDE5;color:#2C3E50;"
                            >
                                <option value="">Select status</option>
                                <option value="Available">Available</option>
                                <option value="Busy">Busy</option>
                                <option value="On_Leave">On Leave</option>
                            </select>
                            <div class="inline-flex items-center gap-2 text-xs font-semibold px-3 py-1 rounded-full" :style="badgeStyle">
                                <span class="inline-block h-2 w-2 rounded-full" :style="dotStyle"></span>
                                <span x-text="availabilityText"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Row 3: Specialization full-width --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-[#2C3E50]">Specialization</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                            @foreach ($specializationOptions as $value => $label)
                                <label class="group flex items-center gap-2 rounded-md border px-3 py-2 text-xs sm:text-sm leading-tight hover:border-[#1F4E79] hover:bg-[#F5F8FC]" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;">
                                    <input
                                        type="checkbox"
                                        name="specialization[]"
                                        value="{{ $value }}"
                                        x-model="form.specialization"
                                        class="accent-[#1F4E79] h-4 w-4"
                                    >
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-[#7F8C8D]">Choose one or more skill areas.</p>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="submit" class="rounded-lg px-4 py-2 font-semibold" style="background:#1F4E79;color:#FFFFFF;">Save profile</button>
                    </div>
                    <div class="text-xs" :class="saveMessageClass" x-text="saveMessage"></div>
                </form>
            </div>

            <div class="rounded-2xl shadow-sm border border-[#D7DDE5] bg-white p-6">
                <h2 class="text-lg font-semibold mb-3 text-[#2C3E50]">Change password</h2>
                <form class="space-y-4" @submit.prevent="savePassword">
                    <div class="space-y-2">
                        <label for="current_password" class="text-sm font-medium text-[#2C3E50]">Current Password</label>
                        <input id="current_password" type="password" x-model="passwordForm.current_password" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" required>
                    </div>
                    <div class="space-y-2">
                        <label for="new_password" class="text-sm font-medium text-[#2C3E50]">New Password</label>
                        <input id="new_password" type="password" x-model="passwordForm.new_password" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" required>
                    </div>
                    <div class="space-y-2">
                        <label for="new_password_confirmation" class="text-sm font-medium text-[#2C3E50]">Confirm New Password</label>
                        <input id="new_password_confirmation" type="password" x-model="passwordForm.new_password_confirmation" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" required>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="submit" class="rounded-lg px-4 py-2 font-semibold" style="background:#1F4E79;color:#FFFFFF;">Update password</button>
                    </div>
                    <div class="text-xs" :class="pwMessageClass" x-text="pwMessage"></div>
                </form>
            </div>
        </div>
    </section>

    <script>
        function profilePage() {
            return {
                form: {
                    name: '',
                    email: '',
                    phone_number: null,
                    phone_number_digits: '',
                    campus: '',
                    specialization: [],
                    availability_status: '',
                },
                passwordForm: {
                    current_password: '',
                    new_password: '',
                    new_password_confirmation: '',
                },
                lastUpdated: '',
                saveMessage: '',
                saveMessageClass: '',
                pwMessage: '',
                pwMessageClass: '',
                get avatarInitial() {
                    return (this.form.name || 'T').slice(0, 1).toUpperCase();
                },
                get availabilityText() {
                    return this.form.availability_status ? this.form.availability_status.replace('_', ' ') : 'Not set';
                },
                get badgeStyle() {
                    const map = {
                        'Available': ['#E8F6F3', '#1E6653'],
                        'Busy': ['#FFF4E5', '#C27B12'],
                        'On_Leave': ['#FCE8E6', '#B23B3B'],
                    };
                    const [bg, text] = map[this.form.availability_status] || ['#F5F7FA', '#2C3E50'];
                    return `background:${bg};color:${text};`;
                },
                get dotStyle() {
                    const map = {
                        'Available': '#1E6653',
                        'Busy': '#C27B12',
                        'On_Leave': '#B23B3B',
                    };
                    const color = map[this.form.availability_status] || '#2C3E50';
                    return `background:${color};`;
                },
                async load() {
                    try {
                        const res = await fetch('/api/tech/profile', { credentials: 'same-origin' });
                        if (!res.ok) throw new Error('Failed to load profile');
                        const json = await res.json();
                        const data = json.data || {};
                        this.form.name = data.name || '';
                        this.form.email = data.email || '';
                        this.form.phone_number = data.phone_number || '';
                        this.form.phone_number_digits = (data.phone_number || '').replace(/^\+60/, '');
                        this.form.campus = data.campus || '';
                        this.form.specialization = data.specialization ? data.specialization.split(',').filter(Boolean) : [];
                        this.form.availability_status = data.availability_status || '';
                        this.lastUpdated = data.updated_at ? new Date(data.updated_at).toLocaleDateString() : '-';
                    } catch (e) {
                        console.error(e);
                        this.saveMessage = 'Failed to load profile';
                        this.saveMessageClass = 'text-red-600';
                    }
                },
                async saveProfile() {
                    this.saveMessage = '';
                    const { email, ...payload } = this.form;
                    const res = await fetch('/api/tech/profile', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content },
                        credentials: 'same-origin',
                        body: JSON.stringify(payload),
                    });
                    if (res.ok) {
                        this.saveMessage = 'Profile updated.';
                        this.saveMessageClass = 'text-green-600';
                    } else {
                        const err = await res.json().catch(() => ({}));
                        this.saveMessage = err.message || 'Failed to update profile';
                        this.saveMessageClass = 'text-red-600';
                    }
                },
                async savePassword() {
                    this.pwMessage = '';
                    const res = await fetch('/api/tech/profile/password', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content },
                        credentials: 'same-origin',
                        body: JSON.stringify(this.passwordForm),
                    });
                    if (res.ok) {
                        this.pwMessage = 'Password updated.';
                        this.pwMessageClass = 'text-green-600';
                        this.passwordForm = { current_password: '', new_password: '', new_password_confirmation: '' };
                    } else {
                        const err = await res.json().catch(() => ({}));
                        const msg = err.errors ? Object.values(err.errors).flat().join(' ') : (err.message || 'Failed to update password');
                        this.pwMessage = msg;
                        this.pwMessageClass = 'text-red-600';
                    }
                },
            };
        }
    </script>
@endsection

@extends('layouts.app')

@php
    $pageTitle = 'Technician Profile';
    $user = $user ?? Auth::user();
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
    <section class="space-y-6">
        <div class="rounded-2xl border border-transparent bg-gradient-to-r from-[#0F172A] via-[#1F4E79] to-[#2A7ABF] text-white p-6 shadow-md">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-full bg-white/20 flex items-center justify-center text-lg font-semibold">
                        {{ strtoupper(substr($user?->name ?? 'T', 0, 1)) }}
                    </div>
                    <div>
                        <div class="text-sm text-white/80">Technician profile</div>
                        <h1 class="text-2xl font-semibold">{{ $user?->name ?? 'Technician' }}</h1>
                        <p class="text-xs text-white/70 mt-1 flex items-center gap-2">
                            <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold" style="background:rgba(255,255,255,0.15);">
                                {{ $user?->role ?? 'Technician' }}
                            </span>
                            <span>Last updated: {{ optional($user?->updated_at)->format('d M Y') ?? '—' }}</span>
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <div class="rounded-xl bg-white/15 px-4 py-3 min-w-[180px]">
                        <div class="text-xs text-white/80">Contact</div>
                        <div class="text-sm font-semibold">{{ $user?->email }}</div>
                        @if($user?->phone_number)
                            <div class="text-xs text-white/80">{{ $user->phone_number }}</div>
                        @endif
                    </div>
                    <div class="rounded-xl bg-white/15 px-4 py-3 min-w-[160px]">
                        <div class="text-xs text-white/80">Campus</div>
                        <div class="text-sm font-semibold">{{ $user?->campus ?: 'Not set' }}</div>
                        <div class="text-xs text-white/70">Availability: {{ str_replace('_',' ', $user?->availability_status ?? 'Not set') }}</div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $phoneDigits = '';
            if (!empty($user?->phone_number)) {
                $phoneDigits = preg_replace('/^\\+60/', '', $user->phone_number);
            }
            $selectedSpecializations = collect(explode(',', $user->specialization ?? ''))
                ->filter()
                ->values()
                ->all();
        @endphp

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 rounded-2xl shadow-sm border border-[#D7DDE5] bg-white p-6">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-[#2C3E50]">Profile details</h2>
                        <p class="text-xs text-[#7F8C8D]">Update your contact info and skill areas.</p>
                    </div>
                    <span class="text-[11px] px-2 py-1 rounded-full" style="background:#F5F7FA;color:#1F4E79;">Tech</span>
                </div>
                <form action="{{ route('technician.profile.update') }}" method="post" class="space-y-4">
                    @csrf
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label for="name" class="text-sm font-medium text-[#2C3E50]">Full Name</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $user?->name) }}" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" required>
                        </div>
                        <div class="space-y-2">
                            <label for="email" class="text-sm font-medium text-[#2C3E50]">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $user?->email) }}" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" required>
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
                                    value="{{ old('phone_number_digits', $phoneDigits) }}"
                                    class="flex-1 px-3 py-2.5 text-sm focus:outline-none border-none"
                                    style="color:#2C3E50;background:transparent;"
                                >
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="campus" class="text-sm font-medium text-[#2C3E50]">Campus</label>
                            <select id="campus" name="campus" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;">
                                <option value="" @selected(old('campus', $user?->campus) === null || old('campus', $user?->campus) === '')>Select campus</option>
                                <option value="Penang" @selected(old('campus', $user?->campus) === 'Penang')>Penang</option>
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
                                class="w-full rounded-lg px-3 py-2 border bg-white focus:outline-none focus:ring-2 focus:ring-[#1F4E79]/40 focus:border-[#1F4E79]"
                                style="border-color:#D7DDE5;color:#2C3E50;"
                            >
                                <option value="">Select status</option>
                                @foreach (['Available', 'Busy', 'On_Leave'] as $opt)
                                    <option value="{{ $opt }}" @selected(old('availability_status', $user?->availability_status) === $opt)>{{ str_replace('_', ' ', $opt) }}</option>
                                @endforeach
                            </select>
                            @php
                                $badgeColors = [
                                    'Available' => ['bg' => '#E8F6F3', 'text' => '#1E6653'],
                                    'Busy' => ['bg' => '#FFF4E5', 'text' => '#C27B12'],
                                    'On_Leave' => ['bg' => '#FCE8E6', 'text' => '#B23B3B'],
                                ];
                                $currentStatus = old('availability_status', $user?->availability_status);
                                $colors = $badgeColors[$currentStatus] ?? ['bg' => '#F5F7FA', 'text' => '#2C3E50'];
                            @endphp
                            <div class="inline-flex items-center gap-2 text-xs font-semibold px-3 py-1 rounded-full" style="background:{{ $colors['bg'] }};color:{{ $colors['text'] }};">
                                <span class="inline-block h-2 w-2 rounded-full" style="background:{{ $colors['text'] }};"></span>
                                {{ $currentStatus ? str_replace('_',' ', $currentStatus) : 'Not set' }}
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
                                        @checked(in_array($value, old('specialization', $selectedSpecializations), true))
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
                </form>
            </div>

            <div class="rounded-2xl shadow-sm border border-[#D7DDE5] bg-white p-6">
                <h2 class="text-lg font-semibold mb-3 text-[#2C3E50]">Change password</h2>
                <form action="{{ route('technician.profile.password') }}" method="post" class="space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label for="current_password" class="text-sm font-medium text-[#2C3E50]">Current Password</label>
                        <input id="current_password" name="current_password" type="password" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" required>
                    </div>
                    <div class="space-y-2">
                        <label for="new_password" class="text-sm font-medium text-[#2C3E50]">New Password</label>
                        <input id="new_password" name="new_password" type="password" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" required>
                    </div>
                    <div class="space-y-2">
                        <label for="new_password_confirmation" class="text-sm font-medium text-[#2C3E50]">Confirm New Password</label>
                        <input id="new_password_confirmation" name="new_password_confirmation" type="password" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" required>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="submit" class="rounded-lg px-4 py-2 font-semibold" style="background:#1F4E79;color:#FFFFFF;">Update password</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

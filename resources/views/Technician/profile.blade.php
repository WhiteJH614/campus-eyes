@extends('layouts.app')

@php
    $pageTitle = 'Technician Profile';
    $user = $user ?? Auth::user();
    $breadcrumbs = [
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'Technician Dashboard', 'url' => route('technician.dashboard')],
        ['label' => 'Profile'],
    ];
@endphp

@section('content')
    <section class="space-y-6">
        <div class="rounded-2xl border border-transparent bg-gradient-to-r from-[#0F172A] via-[#1F4E79] to-[#2A7ABF] text-white p-6 shadow-md">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="text-sm text-white/80">Welcome back</div>
                    <h1 class="text-2xl font-semibold">{{ $user?->full_name ?? 'Technician' }}</h1>
                    <p class="text-xs text-white/70 mt-1">Role: {{ $user?->role ?? 'Technician' }}</p>
                </div>
                <div class="rounded-xl bg-white/15 px-4 py-3">
                    <div class="text-xs text-white/80">Contact</div>
                    <div class="text-sm font-semibold">{{ $user?->email }}</div>
                    @if($user?->phone_number)
                        <div class="text-xs text-white/80">{{ $user->phone_number }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 rounded-2xl shadow-sm border border-[#D7DDE5] bg-white p-6">
                <h2 class="text-lg font-semibold mb-3 text-[#2C3E50]">Profile details</h2>
                <form action="{{ route('technician.profile.update') }}" method="post" class="space-y-4">
                    @csrf
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label for="full_name" class="text-sm font-medium text-[#2C3E50]">Full Name</label>
                            <input id="full_name" name="full_name" type="text" value="{{ old('full_name', $user?->full_name) }}" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" required>
                        </div>
                        <div class="space-y-2">
                            <label for="email" class="text-sm font-medium text-[#2C3E50]">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $user?->email) }}" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" required>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-3 gap-4">
                        <div class="space-y-2">
                            <label for="phone_number" class="text-sm font-medium text-[#2C3E50]">Phone</label>
                            <input id="phone_number" name="phone_number" type="text" value="{{ old('phone_number', $user?->phone_number) }}" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;">
                        </div>
                        <div class="space-y-2">
                            <label for="campus" class="text-sm font-medium text-[#2C3E50]">Campus</label>
                            <input id="campus" name="campus" type="text" value="{{ old('campus', $user?->campus) }}" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;">
                        </div>
                        <div class="space-y-2">
                            <label for="specialization" class="text-sm font-medium text-[#2C3E50]">Specialization</label>
                            <input id="specialization" name="specialization" type="text" value="{{ old('specialization', $user?->specialization) }}" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="availability_status" class="text-sm font-medium text-[#2C3E50]">Availability</label>
                        <select id="availability_status" name="availability_status" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;">
                            <option value="">Select status</option>
                            @foreach (['Available', 'Busy', 'On_Leave'] as $opt)
                                <option value="{{ $opt }}" @selected(old('availability_status', $user?->availability_status) === $opt)>{{ str_replace('_', ' ', $opt) }}</option>
                            @endforeach
                        </select>
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

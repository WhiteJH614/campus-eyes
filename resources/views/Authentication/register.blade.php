@extends('layouts.app')

@php
    $pageTitle = 'Register';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'Register'],
    ];

    // Must match ENUM values in the users table exactly
    $roles = [
        'Reporter' => 'Reporter (Student / Staff)',
        'Technician' => 'Technician',
        'Admin' => 'Admin',
    ];
@endphp

@section('content')
    <section class="max-w-3xl mx-auto space-y-6">
        <div class="rounded-2xl shadow-sm border p-8" style="background:#FFFFFF;border-color:#D7DDE5;">
            <div class="flex flex-col gap-2 mb-6 text-center">
                <h1 class="text-2xl font-semibold" style="color:#2C3E50;">Create your account</h1>
                <p class="text-sm" style="color:#7F8C8D;">Register to start reporting and tracking campus issues.</p>
            </div>

            {{-- Global error summary --}}
            @if ($errors->any())
                <div class="mb-4 rounded-lg border px-4 py-3 text-sm"
                    style="border-color:#E74C3C;color:#C0392B;background:#FDEDEC;">
                    <strong>There were some problems with your input.</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="post" class="space-y-4">
                @csrf

                {{-- Name + Email --}}
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="full_name" class="text-sm font-medium" style="color:#2C3E50;">Full Name</label>
                        <input id="full_name" name="full_name" type="text" value="{{ old('full_name') }}" required
                            class="w-full rounded-lg px-3 py-2 border focus:outline-none"
                            style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
                        @error('full_name')
                            <p class="text-xs mt-1" style="color:#E74C3C;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="email" class="text-sm font-medium" style="color:#2C3E50;">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required
                            class="w-full rounded-lg px-3 py-2 border focus:outline-none"
                            style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
                        @error('email')
                            <p class="text-xs mt-1" style="color:#E74C3C;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Password + Confirm --}}
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="password" class="text-sm font-medium" style="color:#2C3E50;">Password</label>
                        <input id="password" name="password" type="password" required
                            class="w-full rounded-lg px-3 py-2 border focus:outline-none"
                            style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
                        @error('password')
                            <p class="text-xs mt-1" style="color:#E74C3C;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="password_confirmation" class="text-sm font-medium" style="color:#2C3E50;">
                            Confirm Password
                        </label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            class="w-full rounded-lg px-3 py-2 border focus:outline-none"
                            style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
                    </div>
                </div>

                {{-- Role --}}
                <div class="space-y-2">
                    <label class="text-sm font-medium" style="color:#2C3E50;">Role</label>
                    <div class="grid sm:grid-cols-3 gap-2">
                        @foreach ($roles as $value => $label)
                            @php
                                // Default to Reporter
                                $checked = old('role', 'Reporter') === $value;
                            @endphp
                            <label
                                class="flex items-center gap-2 rounded-lg border px-3 py-2 cursor-pointer {{ $checked ? 'ring-1' : '' }}"
                                style="border-color:#D7DDE5;color:#2C3E50;">
                                <input type="radio" name="role" value="{{ $value }}" class="accent-[#1F4E79]"
                                    @checked($checked)>
                                <span class="text-sm">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('role')
                        <p class="text-xs mt-1" style="color:#E74C3C;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div class="space-y-2">
                    <label for="phone_number" class="text-sm font-medium" style="color:#2C3E50;">
                        Phone (optional)
                    </label>
                    <input id="phone_number" name="phone_number" type="tel" value="{{ old('phone_number') }}"
                        class="w-full rounded-lg px-3 py-2 border focus:outline-none"
                        style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
                    @error('phone_number')
                        <p class="text-xs mt-1" style="color:#E74C3C;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- You can later add reporter_role / campus / specialization / etc. here if needed --}}

                <button type="submit" class="w-full rounded-lg px-4 py-2.5 text-sm font-semibold shadow-sm"
                    style="background:#1F4E79;color:#FFFFFF;">
                    Create account
                </button>
            </form>

            <div class="text-sm text-center mt-6" style="color:#7F8C8D;">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold" style="color:#1ABC9C;">Sign in</a>
            </div>
        </div>
    </section>
@endsection
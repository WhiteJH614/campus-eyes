@extends('layouts.app')

@php
    $pageTitle = 'Register';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'Register'],
    ];
    $user = $user ?? ['name' => 'Guest', 'role' => 'guest'];
    $roles = ['reporter' => 'Student / Staff', 'technician' => 'Technician', 'admin' => 'Admin'];
@endphp

@section('content')
    <section class="max-w-3xl mx-auto space-y-6">
        <div class="rounded-2xl shadow-sm border p-8" style="background:#FFFFFF;border-color:#D7DDE5;">
            <div class="flex flex-col gap-2 mb-6 text-center">
                <h1 class="text-2xl font-semibold" style="color:#2C3E50;">Create your account</h1>
                <p class="text-sm" style="color:#7F8C8D;">Register to start reporting and tracking campus issues.</p>
            </div>
            <form action="/register" method="post" class="space-y-4">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="name" class="text-sm font-medium" style="color:#2C3E50;">Full Name</label>
                        <input id="name" name="name" type="text" required class="w-full rounded-lg px-3 py-2 border focus:outline-none" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
                    </div>
                    <div class="space-y-2">
                        <label for="email" class="text-sm font-medium" style="color:#2C3E50;">Email</label>
                        <input id="email" name="email" type="email" required class="w-full rounded-lg px-3 py-2 border focus:outline-none" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="password" class="text-sm font-medium" style="color:#2C3E50;">Password</label>
                        <input id="password" name="password" type="password" required class="w-full rounded-lg px-3 py-2 border focus:outline-none" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
                    </div>
                    <div class="space-y-2">
                        <label for="password_confirmation" class="text-sm font-medium" style="color:#2C3E50;">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full rounded-lg px-3 py-2 border focus:outline-none" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium" style="color:#2C3E50;">Role</label>
                    <div class="grid sm:grid-cols-3 gap-2">
                        @foreach ($roles as $value => $label)
                            <label class="flex items-center gap-2 rounded-lg border px-3 py-2 cursor-pointer" style="border-color:#D7DDE5;color:#2C3E50;">
                                <input type="radio" name="role" value="{{ $value }}" class="accent-[#1F4E79]" @checked($value === 'reporter')>
                                <span class="text-sm">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="space-y-2">
                    <label for="phone" class="text-sm font-medium" style="color:#2C3E50;">Phone (optional)</label>
                    <input id="phone" name="phone" type="tel" class="w-full rounded-lg px-3 py-2 border focus:outline-none" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
                </div>
                <button type="submit" class="w-full rounded-lg px-4 py-2.5 text-sm font-semibold shadow-sm" style="background:#1F4E79;color:#FFFFFF;">Create account</button>
            </form>
            <div class="text-sm text-center mt-6" style="color:#7F8C8D;">
                Already have an account?
                <a href="/login" class="font-semibold" style="color:#1ABC9C;">Sign in</a>
            </div>
        </div>
    </section>
@endsection

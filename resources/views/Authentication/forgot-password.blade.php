@extends('layouts.app')

@php
    $pageTitle = 'Forgot Password';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'Forgot Password'],
    ];
    $user = $user ?? ['name' => 'Guest', 'role' => 'guest'];
@endphp

@section('content')
    <section class="max-w-xl mx-auto space-y-6">
        <div class="rounded-2xl shadow-sm border p-8" style="background:#FFFFFF;border-color:#D7DDE5;">
            <div class="flex flex-col gap-2 mb-6">
                <h1 class="text-2xl font-semibold" style="color:#2C3E50;">Reset your password</h1>
                <p class="text-sm" style="color:#7F8C8D;">Enter the email associated with your account and we will send a reset link.</p>
            </div>
            <form action="/forgot-password" method="post" class="space-y-4">
                <div class="space-y-2">
                    <label for="email" class="text-sm font-medium" style="color:#2C3E50;">Email</label>
                    <input id="email" name="email" type="email" required class="w-full rounded-lg px-3 py-2 border focus:outline-none" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
                </div>
                <button type="submit" class="w-full rounded-lg px-4 py-2.5 text-sm font-semibold shadow-sm" style="background:#1F4E79;color:#FFFFFF;">Send reset link</button>
            </form>
            <div class="text-sm text-center mt-6" style="color:#7F8C8D;">
                Remembered your password?
                <a href="/login" class="font-semibold" style="color:#1ABC9C;">Back to login</a>
            </div>
        </div>
    </section>
@endsection

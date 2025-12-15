@php
    $pageTitle = 'Sign in to Campus Eye';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Login'],
    ];
@endphp

@extends('layouts.app')

@section('content')
    <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
        <div class="space-y-4">
            <p class="text-sm uppercase tracking-wide font-semibold" style="color:#1F4E79;">
                Welcome back
            </p>
            <h1 class="text-3xl font-bold" style="color:#2C3E50;">
                Sign in to manage campus maintenance requests
            </h1>
            <p class="text-base leading-relaxed" style="color:#566573;">
                Access your personalised dashboard, track tasks, and keep facilities in top shape. Use your campus email
                to continue.
            </p>
            <ul class="space-y-2 text-sm" style="color:#566573;">
                <li class="flex items-start gap-2">
                    <span class="mt-1 h-2.5 w-2.5 rounded-full" style="background:#1ABC9C;"></span>
                    Quick access to your assigned reports and tasks.
                </li>
                <li class="flex items-start gap-2">
                    <span class="mt-1 h-2.5 w-2.5 rounded-full" style="background:#1ABC9C;"></span>
                    Secure sign-in with session protection and CSRF safeguards.
                </li>
                <li class="flex items-start gap-2">
                    <span class="mt-1 h-2.5 w-2.5 rounded-full" style="background:#1ABC9C;"></span>
                    Need help? Contact Facilities IT support for assistance.
                </li>
            </ul>
        </div>

        <div class="bg-white shadow-xl rounded-2xl p-8 space-y-6 border" style="border-color:#E5E9F2;">
            @if (session('status'))
                <div class="rounded-lg px-4 py-3 text-sm" style="background:#E8F6F3;color:#1E6653;">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-lg px-4 py-3 text-sm" style="background:#FEECEC;color:#C0392B;">
                    <div class="font-semibold mb-2">We could not sign you in</div>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div class="space-y-2">
                    <label for="email" class="text-sm font-medium" style="color:#2C3E50;">Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        class="w-full rounded-lg border px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                        style="border-color:#D7DDE5;color:#2C3E50;caret-color:#1F4E79;box-shadow:none;"
                    >
                </div>

                <div class="space-y-2">
                    <label for="password" class="text-sm font-medium" style="color:#2C3E50;">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        class="w-full rounded-lg border px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                        style="border-color:#D7DDE5;color:#2C3E50;caret-color:#1F4E79;box-shadow:none;"
                    >
                </div>

                <div class="flex items-center justify-between text-sm" style="color:#566573;">
                    <label for="remember_me" class="inline-flex items-center gap-2">
                        <input
                            id="remember_me"
                            type="checkbox"
                            name="remember"
                            class="rounded border-gray-300 text-sky-700 focus:ring-2 focus:ring-sky-600"
                        >
                        <span>Remember me on this device</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="font-semibold hover:underline" style="color:#1F4E79;">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <button
                    type="submit"
                    class="w-full rounded-lg px-4 py-3 text-sm font-semibold shadow transition focus:outline-none focus:ring-2 focus:ring-offset-2"
                    style="background:linear-gradient(135deg,#1F4E79,#285F96);color:white;box-shadow:0 10px 30px rgba(31,78,121,0.25);"
                >
                    Log in
                </button>

                <p class="text-sm text-center" style="color:#566573;">
                    New to Campus Eye?
                    <a href="{{ route('register') }}" class="font-semibold hover:underline" style="color:#1F4E79;">
                        Create an account
                    </a>
                </p>
            </form>
        </div>
    </div>
@endsection

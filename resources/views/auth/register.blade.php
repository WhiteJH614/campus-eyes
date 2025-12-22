@php
    $pageTitle = 'Create your Campus Eye account';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Register'],
    ];
@endphp

@extends('layouts.app')

@section('content')
    <div class="grid gap-10 lg:grid-cols-2 lg:items-start">
        <div class="space-y-4">
            <p class="text-sm uppercase tracking-wide font-semibold" style="color:#1F4E79;">
                Get started
            </p>
            <h1 class="text-3xl font-bold" style="color:#2C3E50;">
                Join the Campus Eye maintenance community
            </h1>
            <p class="text-base leading-relaxed" style="color:#566573;">
                Create an account to submit issues, monitor progress, and collaborate with technicians and admins to keep
                campus facilities running smoothly.
            </p>
            <div class="grid gap-3 sm:grid-cols-2">
                <div class="rounded-xl p-4 border" style="border-color:#E5E9F2;background:#F9FBFF;">
                    <div class="text-sm font-semibold" style="color:#1F4E79;">Reporters</div>
                    <p class="text-sm mt-1" style="color:#566573;">Log maintenance requests and get live updates.</p>
                </div>
                <div class="rounded-xl p-4 border" style="border-color:#E5E9F2;background:#F5F8FC;">
                    <div class="text-sm font-semibold" style="color:#1F4E79;">Technicians & Admins</div>
                    <p class="text-sm mt-1" style="color:#566573;">Track assignments, close tasks, and manage resources.</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-xl rounded-2xl p-8 space-y-6 border" style="border-color:#E5E9F2;">
            @if ($errors->any())
                <div class="rounded-lg px-4 py-3 text-sm" style="background:#FEECEC;color:#C0392B;">
                    <div class="font-semibold mb-2">We need a few fixes</div>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <div class="space-y-2">
                    <label for="name" class="text-sm font-medium" style="color:#2C3E50;">Full name</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        autocomplete="name"
                        class="w-full rounded-lg border px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                        style="border-color:#D7DDE5;color:#2C3E50;caret-color:#1F4E79;box-shadow:none;"
                    >
                </div>

                <div class="space-y-2">
                    <label for="email" class="text-sm font-medium" style="color:#2C3E50;">Campus email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
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
                        autocomplete="new-password"
                        class="w-full rounded-lg border px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                        style="border-color:#D7DDE5;color:#2C3E50;caret-color:#1F4E79;box-shadow:none;"
                    >
                    <p class="text-xs" style="color:#7F8C8D;">Use at least 8 characters with a mix of letters, numbers, and symbols.</p>
                </div>

                <div class="space-y-2">
                    <label for="password_confirmation" class="text-sm font-medium" style="color:#2C3E50;">Confirm password</label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        required
                        autocomplete="new-password"
                        class="w-full rounded-lg border px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                        style="border-color:#D7DDE5;color:#2C3E50;caret-color:#1F4E79;box-shadow:none;"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full rounded-lg px-4 py-3 text-sm font-semibold shadow transition focus:outline-none focus:ring-2 focus:ring-offset-2"
                    style="background:linear-gradient(135deg,#1F4E79,#285F96);color:white;box-shadow:0 10px 30px rgba(31,78,121,0.25);"
                >
                    Create account
                </button>

                <p class="text-sm text-center" style="color:#566573;">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-semibold hover:underline" style="color:#1F4E79;">
                        Sign in
                    </a>
                </p>
            </form>
        </div>
    </div>
@endsection

<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Reporter Type -->
        <div class="mt-4">
            <x-input-label for="reporter_role" :value="__('I am a')" />
            <select id="reporter_role" name="reporter_role" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                <option value="">{{ __('Select your role...') }}</option>
                <option value="Student" {{ old('reporter_role') == 'Student' ? 'selected' : '' }}>{{ __('Student') }}</option>
                <option value="Staff" {{ old('reporter_role') == 'Staff' ? 'selected' : '' }}>{{ __('Staff') }}</option>
            </select>
            <x-input-error :messages="$errors->get('reporter_role')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>


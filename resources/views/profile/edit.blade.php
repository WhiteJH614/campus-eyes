@extends('layouts.app')

@php
    $pageTitle = __('Profile');
    $breadcrumbs = [
        ['label' => 'Home', 'url' => '/'],
        ['label' => __('Profile')],
    ];
@endphp

@section('content')
    <div class="py-8 space-y-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Radiant Header -->
            <div class="relative overflow-hidden rounded-2xl text-white shadow-lg mb-8"
                style="background:linear-gradient(120deg,#1F4E79,#285F96);">
                <div class="absolute inset-0" style="background:linear-gradient(180deg,rgba(255,255,255,0.08),transparent);"></div>
                <div class="relative px-8 py-10">
                    <h2 class="text-3xl font-bold">Profile Settings</h2>
                    <p class="mt-2 text-lg" style="color:rgba(255,255,255,0.9);">
                        Manage your account information, security, and preferences.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Profile Information -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg border" style="border-color:#D7DDE5;">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <!-- Update Password -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg border" style="border-color:#D7DDE5;">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <!-- Delete Account (Full Width) -->
                <div class="col-span-1 md:col-span-2 p-4 sm:p-8 bg-white shadow sm:rounded-lg border" style="border-color:#D7DDE5;">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
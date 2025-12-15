@php
    use Illuminate\Support\Str;

    $pageTitle = $pageTitle ?? 'Campus Eye Maintenance Reporting System';

    // Use Laravel auth user instead of manual session
    $authUser = auth()->user();

    $name = $authUser?->full_name ?? 'Guest';
    // In DB: 'Reporter', 'Technician', 'Admin'
    $rawRole = $authUser?->role ?? null;
    $role = $rawRole ? strtolower($rawRole) : ''; // reporter / technician / admin / ''

    $breadcrumbs = $breadcrumbs ?? [];

    $roleLinks = [
        'reporter' => [
            ['label' => 'My Dashboard', 'url' => '/reporter/dashboard'],
            ['label' => 'New Report', 'url' => '/reports/create'],
            ['label' => 'My Reports', 'url' => '/reports/mine'],
        ],
        'technician' => [
            ['label' => 'Task Dashboard', 'url' => '/technician/dashboard'],
            ['label' => 'Assigned Jobs', 'url' => '/technician/tasks'],
            ['label' => 'Completed Jobs', 'url' => '/technician/completed'],
            ['label' => 'Profile', 'url' => '/profile'],
        ],
        'admin' => [
            ['label' => 'Admin Dashboard', 'url' => '/admin/dashboard'],
            ['label' => 'All Reports', 'url' => '/admin/reports'],
            ['label' => 'Technicians', 'url' => '/admin/technicians'],
            ['label' => 'Locations', 'url' => '/admin/locations'],
            ['label' => 'Categories', 'url' => '/admin/categories'],
            ['label' => 'Analytics', 'url' => '/admin/analytics'],
        ],
    ];

    // Menu for guests (not logged in)
    $guestLinks = [
        ['label' => 'Home', 'url' => '/'],
        // ['label' => 'Login', 'url' => route('login')],
        // ['label' => 'Register', 'url' => route('register')],
    ];

    // Decide which links to show in the main nav
    if ($authUser && isset($roleLinks[$role])) {
        $links = $roleLinks[$role];
    } else {
        $links = $guestLinks;
    }
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Campus Eye Maintenance Reporting System">
    <title>{{ $pageTitle }}</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen" style="background:#F5F7FA;color:#2C3E50;">
    <!-- <a href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 bg-white text-slate-900 px-3 py-2 rounded shadow">
        Skip to content
    </a> -->

    <header class="sticky top-0 z-30 border-b"
        style="backdrop-filter:blur(10px);background:rgba(255,255,255,0.9);border-color:#D7DDE5;">
        <div class="text-white" style="background:linear-gradient(90deg,#1F4E79,#285F96);">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center gap-3">
                <div class="h-10 w-10 rounded-full flex items-center justify-center font-semibold"
                    style="background:rgba(255,255,255,0.18);color:white;">
                    CE
                </div>
                <div>
                    <div class="text-lg font-semibold">
                        Campus Eye Maintenance Reporting System
                    </div>
                    <div class="text-sm" style="color:rgba(255,255,255,0.85);">
                        Friendly, fast reporting for campus facilities
                    </div>
                </div>
            </div>
        </div>

        <nav class="mx-auto px-4 sm:px-6 lg:px-8" style="background:#1F4E79;color:white;">
            <div class="flex items-center justify-between h-14">
                <div class="flex items-center gap-3">
                    <button type="button"
                        class="sm:hidden inline-flex items-center justify-center p-2 rounded-md hover:bg-white/10 focus:outline-none focus:ring-2"
                        aria-label="Toggle navigation" id="navToggle" style="color:white;">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="hidden sm:flex items-center gap-4">
                        @foreach ($links as $link)
                            <a href="{{ $link['url'] }}"
                                class="text-sm font-medium px-1 py-2 border-b-2 border-transparent hover:border-white focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2"
                                style="color:white;">
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    @auth
                        <div class="text-sm hidden sm:block" style="color:rgba(255,255,255,0.85);">
                            <span class="font-semibold" style="color:white;">{{ $name }}</span>
                            <span style="color:rgba(255,255,255,0.7);">
                                ({{ $rawRole ?? 'User' }})
                            </span>
                        </div>
                        <div class="hidden sm:flex items-center gap-3 text-sm">
                            <a href="/profile" class="hover:underline" style="color:#1ABC9C;">Profile</a>
                            <a href="/change-password" class="hover:underline" style="color:#1ABC9C;">Change Password</a>
                            {{-- If your logout route is POST, wrap in a form instead --}}
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="hover:underline"
                                    style="color:rgba(255,255,255,0.85);">Logout</button>
                            </form>
                        </div>
                    @endauth

                    @guest
                        <div class="hidden sm:flex items-center gap-3 text-sm text-white">
                            <a href="{{ route('login') }}"
                                class="text-sm font-medium px-1 py-2 border-b-2 border-transparent hover:border-white focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2">Login</a>
                            <a href="{{ route('register') }}"
                                class="text-sm font-medium px-1 py-2 border-b-2 border-transparent hover:border-white focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2">Register</a>
                        </div>
                    @endguest
                </div>
            </div>

            <div class="sm:hidden" id="mobileNav" hidden>
                <div class="pt-2 pb-3 space-y-1">
                    @foreach ($links as $link)
                        <a href="{{ $link['url'] }}"
                            class="block px-3 py-2 rounded-md text-sm font-medium hover:bg-white/10" style="color:white;">
                            {{ $link['label'] }}
                        </a>
                    @endforeach

                    @auth
                        <div class="border-t pt-2 mt-2 space-y-1 text-sm" style="border-color:rgba(255,255,255,0.2);">
                            <div class="px-3" style="color:rgba(255,255,255,0.7);">
                                Signed in as
                            </div>
                            <div class="px-3 font-semibold" style="color:white;">
                                {{ $name }} ({{ $rawRole ?? 'User' }})
                            </div>
                            <a href="/profile" class="block px-3 py-2 rounded-md hover:bg-white/10"
                                style="color:#1ABC9C;">Profile</a>
                            <a href="/change-password" class="block px-3 py-2 rounded-md hover:bg-white/10"
                                style="color:#1ABC9C;">Change Password</a>
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-3 py-2 rounded-md hover:bg-white/10"
                                    style="color:rgba(255,255,255,0.85);">
                                    Logout
                                </button>
                            </form>
                        </div>
                    @endauth

                    @guest
                        <div class="border-t pt-2 mt-2 space-y-1 text-sm" style="border-color:rgba(255,255,255,0.2);">
                            <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md hover:bg-white/10"
                                style="color:#1ABC9C;">
                                Login
                            </a>
                            <a href="{{ route('register') }}" class="block px-3 py-2 rounded-md hover:bg-white/10"
                                style="color:#1ABC9C;">
                                Register
                            </a>
                        </div>
                    @endguest
                </div>
            </div>
        </nav>

        @if (!empty($breadcrumbs))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3" style="background:#F5F7FA;">
                <nav class="text-sm" aria-label="Breadcrumb" style="color:#7F8C8D;">
                    <ol class="flex items-center gap-2 flex-wrap">
                        @foreach ($breadcrumbs as $index => $crumb)
                            <li class="flex items-center gap-2">
                                @if (!empty($crumb['url']))
                                    <a href="{{ $crumb['url'] }}" class="hover:underline" style="color:#1F4E79;">
                                        {{ $crumb['label'] }}
                                    </a>
                                @else
                                    <span class="font-semibold" style="color:#2C3E50;">
                                        {{ $crumb['label'] }}
                                    </span>
                                @endif
                                @if ($index < count($breadcrumbs) - 1)
                                    <span style="color:#D7DDE5;">/</span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </nav>
            </div>
        @endif
    </header>

    <main id="main-content" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <footer class="mt-10" style="background:linear-gradient(120deg,#1F4E79,#285F96);color:#FFFFFF;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-2">
                    <div class="text-lg font-semibold">
                        Campus Eye Maintenance Reporting System
                    </div>
                    <p class="text-sm max-w-xl" style="color:rgba(255,255,255,0.85);">
                        Designed to keep campus facilities safe, with clear reporting, tracking, and resolution for
                        everyone.
                    </p>
                    <div class="text-sm" style="color:rgba(255,255,255,0.8);">
                        &copy; 2025 TAR UMT. All rights reserved.
                    </div>
                </div>
                <div class="flex flex-wrap gap-3 text-sm">
                    <a href="/help" class="px-4 py-2 rounded-full border"
                        style="border-color:rgba(255,255,255,0.35);color:white;">
                        Help / FAQ
                    </a>
                    <a href="/support" class="px-4 py-2 rounded-full border"
                        style="border-color:rgba(255,255,255,0.35);color:white;">
                        Contact Support
                    </a>
                    <a href="/privacy" class="px-4 py-2 rounded-full border"
                        style="border-color:rgba(255,255,255,0.35);color:white;">
                        Privacy
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        (() => {
            const toggle = document.getElementById('navToggle');
            const mobileNav = document.getElementById('mobileNav');
            if (toggle && mobileNav) {
                toggle.addEventListener('click', () => {
                    const isHidden = mobileNav.hasAttribute('hidden');
                    if (isHidden) {
                        mobileNav.removeAttribute('hidden');
                    } else {
                        mobileNav.setAttribute('hidden', 'hidden');
                    }
                });
            }
        })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
</body>

</html>
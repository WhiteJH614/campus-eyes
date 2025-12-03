<?php
$pageTitle = $pageTitle ?? 'Campus Eye Maintenance Reporting System';
$user = $user ?? ($_SESSION['user'] ?? []);
$name = $user['name'] ?? 'Guest';
$role = strtolower($user['role'] ?? 'reporter');
$breadcrumbs = $breadcrumbs ?? [];

$roleLinks = [
    'reporter' => [
        ['label' => 'My Dashboard', 'url' => '/reporter/dashboard'],
        ['label' => 'New Report', 'url' => '/reports/create'],
        ['label' => 'My Reports', 'url' => '/reports/mine'],
    ],
    'technician' => [
        ['label' => 'Task Dashboard', 'url' => '/CampusEyes/views/Technician/dashboard.php'],
        ['label' => 'Assigned Tasks', 'url' => '/CampusEyes/views/Technician/tasks.php'],
        ['label' => 'Completed Tasks', 'url' => '/CampusEyes/views/Technician/completed.php'],
        ['label' => 'Profile', 'url' => '/CampusEyes/views/Technician/profile.php'],
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

$links = $roleLinks[$role] ?? $roleLinks['reporter'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Campus Eye Maintenance Reporting System">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen" style="background:#F5F7FA;color:#2C3E50;">
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 bg-white text-slate-900 px-3 py-2 rounded shadow">Skip
        to content</a>
    <header class="sticky top-0 z-30 border-b"
        style="backdrop-filter:blur(10px);background:rgba(255,255,255,0.9);border-color:#D7DDE5;">
        <div class="text-white" style="background:linear-gradient(90deg,#1F4E79,#285F96);">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center gap-3">
                <div class="h-10 w-10 rounded-full flex items-center justify-center font-semibold"
                    style="background:rgba(255,255,255,0.18);color:white;">CE</div>
                <div>
                    <div class="text-lg font-semibold">Campus Eye Maintenance Reporting System</div>
                    <div class="text-sm" style="color:rgba(255,255,255,0.85);">Friendly, fast reporting for campus
                        facilities</div>
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
                        <?php foreach ($links as $link): ?>
                            <a href="<?php echo htmlspecialchars($link['url']); ?>"
                                class="text-sm font-medium px-1 py-2 border-b-2 border-transparent hover:border-white focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2"
                                style="color:white;">
                                <?php echo htmlspecialchars($link['label']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-sm hidden sm:block" style="color:rgba(255,255,255,0.85);">
                        <span class="font-semibold" style="color:white;"><?php echo htmlspecialchars($name); ?></span>
                        <span style="color:rgba(255,255,255,0.7);">(<?php echo ucfirst($role); ?>)</span>
                    </div>
                    <div class="hidden sm:flex items-center gap-3 text-sm">
                        <a href="/profile" class="hover:underline" style="color:#1ABC9C;">Profile</a>
                        <a href="/change-password" class="hover:underline" style="color:#1ABC9C;">Change Password</a>
                        <a href="/logout" class="hover:underline" style="color:rgba(255,255,255,0.85);">Logout</a>
                    </div>
                </div>
            </div>
            <div class="sm:hidden" id="mobileNav" hidden>
                <div class="pt-2 pb-3 space-y-1">
                    <?php foreach ($links as $link): ?>
                        <a href="<?php echo htmlspecialchars($link['url']); ?>"
                            class="block px-3 py-2 rounded-md text-sm font-medium hover:bg-white/10" style="color:white;">
                            <?php echo htmlspecialchars($link['label']); ?>
                        </a>
                    <?php endforeach; ?>
                    <div class="border-t pt-2 mt-2 space-y-1 text-sm" style="border-color:rgba(255,255,255,0.2);">
                        <div class="px-3" style="color:rgba(255,255,255,0.7);">Signed in as</div>
                        <div class="px-3 font-semibold" style="color:white;"><?php echo htmlspecialchars($name); ?>
                            (<?php echo ucfirst($role); ?>)</div>
                        <a href="/profile" class="block px-3 py-2 rounded-md hover:bg-white/10"
                            style="color:#1ABC9C;">Profile</a>
                        <a href="/change-password" class="block px-3 py-2 rounded-md hover:bg-white/10"
                            style="color:#1ABC9C;">Change Password</a>
                        <a href="/logout" class="block px-3 py-2 rounded-md hover:bg-white/10"
                            style="color:rgba(255,255,255,0.85);">Logout</a>
                    </div>
                </div>
            </div>
        </nav>
        <?php if (!empty($breadcrumbs)): ?>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3" style="background:#F5F7FA;">
                <nav class="text-sm" aria-label="Breadcrumb" style="color:#7F8C8D;">
                    <ol class="flex items-center gap-2 flex-wrap">
                        <?php foreach ($breadcrumbs as $index => $crumb): ?>
                            <li class="flex items-center gap-2">
                                <?php if (!empty($crumb['url'])): ?>
                                    <a href="<?php echo htmlspecialchars($crumb['url']); ?>" class="hover:underline"
                                        style="color:#1F4E79;">
                                        <?php echo htmlspecialchars($crumb['label']); ?>
                                    </a>
                                <?php else: ?>
                                    <span class="font-semibold"
                                        style="color:#2C3E50;"><?php echo htmlspecialchars($crumb['label']); ?></span>
                                <?php endif; ?>
                                <?php if ($index < count($breadcrumbs) - 1): ?>
                                    <span style="color:#D7DDE5;">/</span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </nav>
            </div>
        <?php endif; ?>
    </header>
    <main id="main-content" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
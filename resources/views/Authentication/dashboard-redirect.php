<?php
session_start();
$user = $_SESSION['user'] ?? ['role' => 'guest'];
$role = strtolower($user['role'] ?? 'guest');
$map = [
    'reporter' => '/reporter/dashboard',
    'technician' => '/tech/dashboard',
    'admin' => '/admin/dashboard'
];
if (isset($map[$role])) {
    header('Location: ' . $map[$role]);
    exit;
}
// Fallback if no role or unknown role
$pageTitle = 'Choose your dashboard';
$breadcrumbs = [ ['label' => 'Home', 'url' => '/'], ['label' => 'Dashboard'] ];
include __DIR__ . '/../header.php';
?>
<section class="max-w-3xl mx-auto space-y-6">
    <div class="rounded-2xl shadow-sm border p-8 text-center" style="background:#FFFFFF;border-color:#D7DDE5;">
        <h1 class="text-2xl font-semibold mb-2" style="color:#2C3E50;">Select a dashboard</h1>
        <p class="text-sm mb-6" style="color:#7F8C8D;">We could not detect your role. Please pick where you want to go.</p>
        <div class="grid sm:grid-cols-3 gap-3">
            <a href="/reporter/dashboard" class="rounded-lg px-4 py-3 text-sm font-semibold shadow-sm text-center" style="background:#FFFFFF;border:1px solid #D7DDE5;color:#1F4E79;">Reporter</a>
            <a href="/tech/dashboard" class="rounded-lg px-4 py-3 text-sm font-semibold shadow-sm text-center" style="background:#FFFFFF;border:1px solid #D7DDE5;color:#1F4E79;">Technician</a>
            <a href="/admin/dashboard" class="rounded-lg px-4 py-3 text-sm font-semibold shadow-sm text-center" style="background:#1F4E79;color:#FFFFFF;">Admin</a>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../footer.php'; ?>

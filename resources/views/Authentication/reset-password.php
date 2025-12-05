<?php
session_start();
$pageTitle = 'Reset Password';
$breadcrumbs = [ ['label' => 'Home', 'url' => '/'], ['label' => 'Reset Password'] ];
$user = ['name' => 'Guest', 'role' => 'guest'];
include __DIR__ . '/../header.php';
?>
<section class="max-w-xl mx-auto space-y-6">
    <div class="rounded-2xl shadow-sm border p-8" style="background:#FFFFFF;border-color:#D7DDE5;">
        <div class="flex flex-col gap-2 mb-6">
            <h1 class="text-2xl font-semibold" style="color:#2C3E50;">Create a new password</h1>
            <p class="text-sm" style="color:#7F8C8D;">Enter your new password and confirm to finish resetting.</p>
        </div>
        <form action="/reset-password" method="post" class="space-y-4">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>" />
            <div class="space-y-2">
                <label for="email" class="text-sm font-medium" style="color:#2C3E50;">Email</label>
                <input id="email" name="email" type="email" required class="w-full rounded-lg px-3 py-2 border focus:outline-none" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
            </div>
            <div class="space-y-2">
                <label for="password" class="text-sm font-medium" style="color:#2C3E50;">New Password</label>
                <input id="password" name="password" type="password" required class="w-full rounded-lg px-3 py-2 border focus:outline-none" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
            </div>
            <div class="space-y-2">
                <label for="password_confirmation" class="text-sm font-medium" style="color:#2C3E50;">Confirm New Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full rounded-lg px-3 py-2 border focus:outline-none" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
            </div>
            <button type="submit" class="w-full rounded-lg px-4 py-2.5 text-sm font-semibold shadow-sm" style="background:#1F4E79;color:#FFFFFF;">Update password</button>
        </form>
        <div class="text-sm text-center mt-6" style="color:#7F8C8D;">
            <a href="/login" class="font-semibold" style="color:#1ABC9C;">Back to login</a>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../footer.php'; ?>

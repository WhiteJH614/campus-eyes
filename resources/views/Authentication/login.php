<?php
session_start();
$pageTitle = 'Login';
$breadcrumbs = [ ['label' => 'Home', 'url' => '/'], ['label' => 'Login'] ];
$user = ['name' => 'Guest', 'role' => 'guest'];
include __DIR__ . '/../header.php';
?>
<section class="max-w-3xl mx-auto space-y-6">
    <div class="rounded-2xl shadow-sm border p-8" style="background:#FFFFFF;border-color:#D7DDE5;">
        <div class="flex flex-col gap-2 mb-6 text-center">
            <h1 class="text-2xl font-semibold" style="color:#2C3E50;">Welcome back</h1>
            <p class="text-sm" style="color:#7F8C8D;">Sign in to report, track, or manage campus issues.</p>
        </div>
        <form action="/login" method="post" class="space-y-4">
            <div class="space-y-2">
                <label for="email" class="text-sm font-medium" style="color:#2C3E50;">Email</label>
                <input id="email" name="email" type="email" required class="w-full rounded-lg px-3 py-2 border focus:outline-none" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
            </div>
            <div class="space-y-2">
                <label for="password" class="text-sm font-medium" style="color:#2C3E50;">Password</label>
                <input id="password" name="password" type="password" required class="w-full rounded-lg px-3 py-2 border focus:outline-none" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
            </div>
            <div class="space-y-2">
                <label class="text-sm font-medium" style="color:#2C3E50;">Role</label>
                <div class="grid sm:grid-cols-3 gap-2">
                    <?php $roles = ['reporter' => 'Student / Staff', 'technician' => 'Technician', 'admin' => 'Admin']; ?>
                    <?php foreach ($roles as $value => $label): ?>
                        <label class="flex items-center gap-2 rounded-lg border px-3 py-2 cursor-pointer" style="border-color:#D7DDE5;color:#2C3E50;">
                            <input type="radio" name="role" value="<?php echo $value; ?>" class="accent-[#1F4E79]" <?php echo $value === 'reporter' ? 'checked' : ''; ?>>
                            <span class="text-sm"><?php echo $label; ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-sm">
                <label class="inline-flex items-center gap-2" style="color:#2C3E50;">
                    <input type="checkbox" name="remember" class="accent-[#1F4E79]">
                    Remember me
                </label>
                <a href="/forgot-password" class="font-medium" style="color:#1F4E79;">Forgot password?</a>
            </div>
            <button type="submit" class="w-full rounded-lg px-4 py-2.5 text-sm font-semibold shadow-sm" style="background:#1F4E79;color:#FFFFFF;">Sign in</button>
        </form>
        <div class="text-sm text-center mt-6" style="color:#7F8C8D;">
            Don't have an account?
            <a href="/register" class="font-semibold" style="color:#1ABC9C;">Register</a>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../footer.php'; ?>

<?php
session_start();
$pageTitle = 'Technician Profile';
$user = $_SESSION['user'] ?? ['name' => 'Technician', 'role' => 'technician'];
$breadcrumbs = [ ['label' => 'Home', 'url' => '/'], ['label' => 'Profile'] ];
include __DIR__ . '/../header.php';
?>
<section class="space-y-6">
    <div class="rounded-2xl shadow-sm border p-6" style="background:#FFFFFF;border-color:#D7DDE5;">
        <div class="flex flex-col gap-2 mb-4">
            <h1 class="text-2xl font-semibold" style="color:#2C3E50;">Profile</h1>
            <p class="text-sm" style="color:#7F8C8D;">Update your contact details, preferences, and notifications.</p>
        </div>
        <form action="/tech/profile" method="post" class="space-y-4">
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label for="name" class="text-sm font-medium" style="color:#2C3E50;">Full Name</label>
                    <input id="name" name="name" type="text" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
                </div>
                <div class="space-y-2">
                    <label for="email" class="text-sm font-medium" style="color:#2C3E50;">Email</label>
                    <input id="email" name="email" type="email" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
                </div>
            </div>
            <div class="grid sm:grid-cols-3 gap-4">
                <div class="space-y-2">
                    <label for="phone" class="text-sm font-medium" style="color:#2C3E50;">Phone</label>
                    <input id="phone" name="phone" type="tel" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
                </div>
                <div class="space-y-2">
                    <label for="extension" class="text-sm font-medium" style="color:#2C3E50;">Extension (optional)</label>
                    <input id="extension" name="extension" type="text" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
                </div>
                <div class="space-y-2">
                    <label for="department" class="text-sm font-medium" style="color:#2C3E50;">Department</label>
                    <input id="department" name="department" type="text" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
                </div>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label for="location" class="text-sm font-medium" style="color:#2C3E50;">Base Location</label>
                    <input id="location" name="location" type="text" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
                </div>
                <div class="space-y-2">
                    <label for="preferences" class="text-sm font-medium" style="color:#2C3E50;">Preferred blocks/categories</label>
                    <select id="preferences" name="preferences[]" multiple class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;">
                        <option>Electrical</option>
                        <option>IT</option>
                        <option>HVAC</option>
                        <option>Plumbing</option>
                        <option>Block A</option>
                        <option>Block B</option>
                        <option>Block C</option>
                    </select>
                    <p class="text-xs" style="color:#7F8C8D;">Helps admins auto-assign relevant tasks.</p>
                </div>
            </div>
            <div class="space-y-2">
                <label for="skills" class="text-sm font-medium" style="color:#2C3E50;">Skills / Notes</label>
                <textarea id="skills" name="skills" rows="3" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;"></textarea>
            </div>
            <fieldset class="rounded-xl border p-4" style="border-color:#D7DDE5;">
                <legend class="text-sm font-semibold px-1" style="color:#2C3E50;">Notifications</legend>
                <label class="flex items-center gap-2 text-sm" style="color:#2C3E50;">
                    <input type="checkbox" name="notify_assign" class="accent-[#1F4E79]"> Email me when a new task is assigned
                </label>
                <label class="flex items-center gap-2 text-sm mt-2" style="color:#2C3E50;">
                    <input type="checkbox" name="notify_escalated" class="accent-[#1F4E79]"> Email me when a task is escalated or re-opened
                </label>
            </fieldset>
            <div class="flex justify-end gap-2">
                <button type="submit" class="rounded-lg px-4 py-2 font-semibold" style="background:#1F4E79;color:#FFFFFF;">Save profile</button>
            </div>
        </form>
    </div>

    <div class="rounded-2xl shadow-sm border p-6" style="background:#FFFFFF;border-color:#D7DDE5;">
        <h2 class="text-lg font-semibold mb-3" style="color:#2C3E50;">Change password</h2>
        <form action="/tech/profile/password" method="post" class="space-y-4">
            <div class="space-y-2">
                <label for="current_password" class="text-sm font-medium" style="color:#2C3E50;">Current Password</label>
                <input id="current_password" name="current_password" type="password" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label for="new_password" class="text-sm font-medium" style="color:#2C3E50;">New Password</label>
                    <input id="new_password" name="new_password" type="password" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
                </div>
                <div class="space-y-2">
                    <label for="confirm_password" class="text-sm font-medium" style="color:#2C3E50;">Confirm New Password</label>
                    <input id="confirm_password" name="confirm_password" type="password" class="w-full rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button type="submit" class="rounded-lg px-4 py-2 font-semibold" style="background:#1F4E79;color:#FFFFFF;">Update password</button>
            </div>
        </form>
    </div>
</section>
<?php include __DIR__ . '/../footer.php'; ?>

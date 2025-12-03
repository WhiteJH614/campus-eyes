<?php
session_start();
$pageTitle = 'Campus Eye - Home';
$user = $_SESSION['user'] ?? ['name' => 'Guest', 'role' => 'reporter'];
$breadcrumbs = [
    ['label' => 'Home']
];

include 'header.php';
?>
<section class="space-y-8">
    <div class="relative overflow-hidden rounded-2xl text-white shadow-lg" style="background:linear-gradient(120deg,#1F4E79,#285F96);">
        <div class="absolute inset-0" style="background:linear-gradient(180deg,rgba(255,255,255,0.08),transparent);"></div>
        <div class="relative px-6 py-10 sm:px-10 sm:py-12 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-2xl space-y-3">
                <p class="text-sm uppercase tracking-wide" style="color:rgba(255,255,255,0.8);">Campus Eye</p>
                <h1 class="text-3xl sm:text-4xl font-semibold leading-tight">Report, track, and resolve campus facility issues with confidence.</h1>
                <p class="text-base" style="color:rgba(255,255,255,0.85);">A friendly, consistent workflow for reporters, technicians, and admins to keep campus spaces safe and working.</p>
                <div class="flex flex-wrap gap-3 pt-2">
                    <a href="/reports/create" class="inline-flex items-center gap-2 px-4 py-2.5 font-semibold text-sm rounded-lg shadow-sm focus:outline-none focus-visible:ring-2" style="background:#FFFFFF;color:#1F4E79;">
                        Create a report
                    </a>
                    <a href="/reports/mine" class="inline-flex items-center gap-2 px-4 py-2.5 font-medium text-sm rounded-lg border focus:outline-none focus-visible:ring-2" style="background:rgba(255,255,255,0.08);border-color:rgba(255,255,255,0.3);color:#FFFFFF;">
                        View my reports
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 text-left lg:text-right">
                <div class="rounded-xl px-4 py-3 border" style="background:rgba(255,255,255,0.12);border-color:rgba(255,255,255,0.25);">
                    <div class="text-xs" style="color:rgba(255,255,255,0.75);">Active issues</div>
                    <div class="text-2xl font-semibold">12</div>
                </div>
                <div class="rounded-xl px-4 py-3 border" style="background:rgba(255,255,255,0.12);border-color:rgba(255,255,255,0.25);">
                    <div class="text-xs" style="color:rgba(255,255,255,0.75);">Resolved this week</div>
                    <div class="text-2xl font-semibold">27</div>
                </div>
                <div class="rounded-xl px-4 py-3 border col-span-2" style="background:rgba(255,255,255,0.12);border-color:rgba(255,255,255,0.25);">
                    <div class="text-xs" style="color:rgba(255,255,255,0.75);">Average response time</div>
                    <div class="text-2xl font-semibold">2h 15m</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-xl border p-5 shadow-sm hover:shadow-md transition" style="background:#FFFFFF;border-color:#D7DDE5;">
            <div class="text-sm font-semibold" style="color:#1F4E79;">Report an Issue</div>
            <h2 class="text-lg font-semibold mt-1" style="color:#2C3E50;">Clear steps to submit</h2>
            <p class="text-sm mt-2" style="color:#7F8C8D;">Share location, description, and a photo so technicians can act quickly.</p>
            <a href="/reports/create" class="inline-flex items-center gap-1 text-sm font-medium mt-3" style="color:#1ABC9C;">Create report -></a>
        </div>
        <div class="rounded-xl border p-5 shadow-sm hover:shadow-md transition" style="background:#FFFFFF;border-color:#D7DDE5;">
            <div class="text-sm font-semibold" style="color:#3498DB;">Track Progress</div>
            <h2 class="text-lg font-semibold mt-1" style="color:#2C3E50;">Live status and notes</h2>
            <p class="text-sm mt-2" style="color:#7F8C8D;">Follow technician updates, attachments, and resolution history.</p>
            <a href="/reports/mine" class="inline-flex items-center gap-1 text-sm font-medium mt-3" style="color:#1F4E79;">View my reports -></a>
        </div>
        <div class="rounded-xl border p-5 shadow-sm hover:shadow-md transition" style="background:#FFFFFF;border-color:#D7DDE5;">
            <div class="text-sm font-semibold" style="color:#1ABC9C;">Need Help?</div>
            <h2 class="text-lg font-semibold mt-1" style="color:#2C3E50;">Friendly support</h2>
            <p class="text-sm mt-2" style="color:#7F8C8D;">Browse FAQs or reach support for guidance on reports and accounts.</p>
            <a href="/help" class="inline-flex items-center gap-1 text-sm font-medium mt-3" style="color:#1ABC9C;">Help center -></a>
        </div>
    </div>

    <div class="rounded-2xl border shadow-sm p-6" style="background:#FFFFFF;border-color:#D7DDE5;">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-xl font-semibold" style="color:#2C3E50;">How it works</h3>
                <p class="text-sm" style="color:#7F8C8D;">Three simple steps keep everyone aligned and informed.</p>
            </div>
            <div class="flex gap-3 text-sm">
                <span class="inline-flex items-center px-3 py-1 rounded-full" style="background:#D1F2EB;color:#1ABC9C;">Consistent</span>
                <span class="inline-flex items-center px-3 py-1 rounded-full" style="background:#F5F7FA;color:#3498DB;">Transparent</span>
                <span class="inline-flex items-center px-3 py-1 rounded-full" style="background:#FCEED3;color:#F39C12;">Responsive</span>
            </div>
        </div>
        <div class="mt-6 grid gap-4 sm:grid-cols-3">
            <div class="p-4 rounded-xl border" style="background:#F5F7FA;border-color:#D7DDE5;">
                <div class="text-sm font-semibold" style="color:#1F4E79;">1. Submit</div>
                <p class="text-sm mt-2" style="color:#2C3E50;">Reporters share location, category, and a photo to capture the issue clearly.</p>
            </div>
            <div class="p-4 rounded-xl border" style="background:#F5F7FA;border-color:#D7DDE5;">
                <div class="text-sm font-semibold" style="color:#3498DB;">2. Assign</div>
                <p class="text-sm mt-2" style="color:#2C3E50;">Admins route tasks to the right technician and set priority for faster handling.</p>
            </div>
            <div class="p-4 rounded-xl border" style="background:#F5F7FA;border-color:#D7DDE5;">
                <div class="text-sm font-semibold" style="color:#27AE60;">3. Resolve</div>
                <p class="text-sm mt-2" style="color:#2C3E50;">Technicians update status and notes so everyone stays informed until closure.</p>
            </div>
        </div>
    </div>
</section>
<?php include 'footer.php'; ?>

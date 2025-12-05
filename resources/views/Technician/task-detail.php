<?php
session_start();
$pageTitle = 'Task Detail';
$user = $_SESSION['user'] ?? ['name' => 'Technician', 'role' => 'technician'];
$breadcrumbs = [ ['label' => 'Home', 'url' => '/'], ['label' => 'Assigned Tasks', 'url' => '/tech/tasks'], ['label' => 'Task Detail'] ];
include __DIR__ . '/../header.php';

$task = [
    'id' => 'R-210',
    'location' => 'Block A, Lab 2, Room 204',
    'block' => 'Block A',
    'floor' => 'Level 2',
    'room' => 'Lab 2',
    'category' => 'Electrical',
    'urgency' => 'High',
    'status' => 'In Progress',
    'reported_at' => '2025-12-01 09:15',
    'assigned_at' => '2025-12-02 08:30',
    'due_at' => '2025-12-04 17:00',
    'description' => 'Projector not turning on. Power outlet seems fine, possible cable or bulb issue.',
    'photo_before' => '/uploads/sample-photo.jpg',
    'reporter' => ['name' => 'John Tan', 'phone' => '+60 12-345 6789'],
];
?>
<section class="space-y-6">
    <div class="rounded-2xl shadow-sm border p-6" style="background:#FFFFFF;border-color:#D7DDE5;">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4">
            <div>
                <h1 class="text-2xl font-semibold" style="color:#2C3E50;">Ticket <?php echo htmlspecialchars($task['id']); ?></h1>
                <p class="text-sm" style="color:#7F8C8D;">Technician view and actions.</p>
            </div>
            <div class="flex flex-wrap gap-2 text-sm">
                <span class="px-3 py-1 rounded-full font-semibold" style="background:#E74C3C;color:#FFFFFF;">High urgency</span>
                <span class="px-3 py-1 rounded-full font-semibold" style="background:#3498DB;color:#FFFFFF;">In Progress</span>
                <span class="px-3 py-1 rounded-full font-semibold" style="background:#F1C40F;color:#2C3E50;">Due: <?php echo htmlspecialchars($task['due_at']); ?></span>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <div class="text-sm font-semibold" style="color:#2C3E50;">Location</div>
                <div style="color:#2C3E50;"><?php echo htmlspecialchars($task['location']); ?></div>
                <div class="text-sm" style="color:#7F8C8D;">Block: <?php echo htmlspecialchars($task['block']); ?> · Floor: <?php echo htmlspecialchars($task['floor']); ?> · Room: <?php echo htmlspecialchars($task['room']); ?></div>
            </div>
            <div class="space-y-2">
                <div class="text-sm font-semibold" style="color:#2C3E50;">Category</div>
                <div style="color:#2C3E50;"><?php echo htmlspecialchars($task['category']); ?></div>
            </div>
            <div class="space-y-2">
                <div class="text-sm font-semibold" style="color:#2C3E50;">Reported at</div>
                <div style="color:#2C3E50;"><?php echo htmlspecialchars($task['reported_at']); ?></div>
            </div>
            <div class="space-y-2">
                <div class="text-sm font-semibold" style="color:#2C3E50;">Assigned at</div>
                <div style="color:#2C3E50;"><?php echo htmlspecialchars($task['assigned_at']); ?></div>
            </div>
            <div class="space-y-2">
                <div class="text-sm font-semibold" style="color:#2C3E50;">Reporter contact</div>
                <div style="color:#2C3E50;"><?php echo htmlspecialchars($task['reporter']['name']); ?> · <?php echo htmlspecialchars($task['reporter']['phone']); ?></div>
            </div>
        </div>

        <div class="mt-4">
            <div class="text-sm font-semibold mb-1" style="color:#2C3E50;">Description</div>
            <p class="text-sm" style="color:#2C3E50;"><?php echo htmlspecialchars($task['description']); ?></p>
        </div>

        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div>
                <div class="text-sm font-semibold mb-1" style="color:#2C3E50;">Before photo</div>
                <div class="rounded-lg border aspect-video flex items-center justify-center" style="border-color:#D7DDE5;background:#F5F7FA;">
                    <span class="text-sm" style="color:#7F8C8D;">Preview</span>
                </div>
            </div>
            <div>
                <div class="text-sm font-semibold mb-1" style="color:#2C3E50;">After repair photo (optional)</div>
                <label class="rounded-lg border aspect-video flex items-center justify-center cursor-pointer" style="border-color:#D7DDE5;background:#F5F7FA;color:#1F4E79;">
                    <input type="file" name="after_photo" class="hidden" />
                    <span class="text-sm">Upload proof</span>
                </label>
            </div>
        </div>
    </div>

    <div class="rounded-2xl shadow-sm border p-6" style="background:#FFFFFF;border-color:#D7DDE5;">
        <h2 class="text-lg font-semibold mb-3" style="color:#2C3E50;">Status & time tracking</h2>
        <form action="/tech/tasks/<?php echo urlencode($task['id']); ?>/status" method="post" class="space-y-4">
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-sm font-medium" style="color:#2C3E50;">Next status</label>
                    <div class="grid sm:grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 rounded-lg border px-3 py-2" style="border-color:#D7DDE5;color:#2C3E50;">
                            <input type="radio" name="status" value="In Progress" class="accent-[#1F4E79]" checked>
                            <span class="text-sm">In Progress</span>
                        </label>
                        <label class="flex items-center gap-2 rounded-lg border px-3 py-2" style="border-color:#D7DDE5;color:#2C3E50;">
                            <input type="radio" name="status" value="Completed" class="accent-[#1F4E79]">
                            <span class="text-sm">Completed</span>
                        </label>
                        <label class="flex items-center gap-2 rounded-lg border px-3 py-2" style="border-color:#D7DDE5;color:#2C3E50;">
                            <input type="radio" name="status" value="Escalated" class="accent-[#1F4E79]">
                            <span class="text-sm">Escalated / Cannot Resolve</span>
                        </label>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium" style="color:#2C3E50;">Time tracking</label>
                    <div class="grid sm:grid-cols-2 gap-2">
                        <input type="datetime-local" name="start_time" class="rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" placeholder="Start time" />
                        <input type="datetime-local" name="completion_time" class="rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" placeholder="Completion time" />
                    </div>
                    <p class="text-xs" style="color:#7F8C8D;">Total duration is calculated from start and completion times.</p>
                </div>
            </div>
            <div class="space-y-2">
                <label for="notes" class="text-sm font-medium" style="color:#2C3E50;">Resolution notes</label>
                <textarea id="notes" name="notes" rows="4" class="w-full rounded-lg px-3 py-2 border focus:outline-none" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;"></textarea>
            </div>
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="text-sm font-medium" style="color:#2C3E50;">Parts used (optional)</label>
                    <button type="button" class="text-sm font-semibold" style="color:#1F4E79;">Add part</button>
                </div>
                <div class="rounded-lg border p-3" style="border-color:#D7DDE5;background:#F5F7FA;">
                    <div class="text-sm" style="color:#7F8C8D;">Example: LED Tube x1</div>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between text-sm" style="color:#7F8C8D;">
                <div>Only the assigned technician or admin can update this task.</div>
                <div class="flex gap-2">
                    <button type="submit" class="rounded-lg px-4 py-2 font-semibold" style="background:#1F4E79;color:#FFFFFF;">Save update</button>
                    <a href="/tech/tasks" class="rounded-lg px-4 py-2 font-semibold border" style="border-color:#D7DDE5;color:#1F4E79;">Back to tasks</a>
                </div>
            </div>
        </form>
    </div>
</section>
<?php include __DIR__ . '/../footer.php'; ?>

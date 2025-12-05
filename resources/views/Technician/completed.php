<?php
session_start();
$pageTitle = 'Completed Tasks';
$user = $_SESSION['user'] ?? ['name' => 'Technician', 'role' => 'technician'];
$breadcrumbs = [ ['label' => 'Home', 'url' => '/'], ['label' => 'Completed Tasks'] ];
include __DIR__ . '/../header.php';

$summary = [
    'total' => 32,
    'avg_time' => '2h 45m',
    'high_urgency' => 6
];
?>
<section class="space-y-6">
    <div class="rounded-2xl shadow-sm border p-6" style="background:#FFFFFF;border-color:#D7DDE5;">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold" style="color:#2C3E50;">Completed Tasks</h1>
                <p class="text-sm" style="color:#7F8C8D;">History of tasks you have completed with filters and performance stats.</p>
            </div>
            <div class="flex flex-wrap gap-2 text-sm">
                <a href="?range=7" class="px-3 py-2 rounded-lg border" style="background:#FFFFFF;border-color:#D7DDE5;color:#1F4E79;">Last 7 days</a>
                <a href="?range=30" class="px-3 py-2 rounded-lg border" style="background:#FFFFFF;border-color:#D7DDE5;color:#1F4E79;">Last 30 days</a>
            </div>
        </div>
        <form class="mt-4 grid gap-3 lg:grid-cols-4" method="get">
            <input type="date" name="from" class="rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
            <input type="date" name="to" class="rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
            <select name="category" class="rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;">
                <option value="">Category</option>
                <option>Electrical</option>
                <option>IT</option>
                <option>HVAC</option>
            </select>
            <select name="block" class="rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;">
                <option value="">Block</option>
                <option>Block A</option>
                <option>Block B</option>
                <option>Block C</option>
                <option>Block M</option>
            </select>
            <input type="text" name="q" placeholder="Search notes (lamp, projector)" class="rounded-lg px-3 py-2 border" style="border-color:#D7DDE5;color:#2C3E50;background:#FFFFFF;" />
            <button type="submit" class="rounded-lg px-4 py-2 font-semibold" style="background:#1F4E79;color:#FFFFFF;">Apply</button>
        </form>

        <div class="mt-6 grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl p-4" style="background:#F5F7FA;border:1px solid #D7DDE5;">
                <div class="text-sm" style="color:#7F8C8D;">Total completed</div>
                <div class="text-2xl font-semibold" style="color:#2C3E50;"><?php echo htmlspecialchars($summary['total']); ?></div>
            </div>
            <div class="rounded-xl p-4" style="background:#F5F7FA;border:1px solid #D7DDE5;">
                <div class="text-sm" style="color:#7F8C8D;">Avg resolution time</div>
                <div class="text-2xl font-semibold" style="color:#2C3E50;"><?php echo htmlspecialchars($summary['avg_time']); ?></div>
            </div>
            <div class="rounded-xl p-4" style="background:#F5F7FA;border:1px solid #D7DDE5;">
                <div class="text-sm" style="color:#7F8C8D;">High urgency completed</div>
                <div class="text-2xl font-semibold" style="color:#2C3E50;"><?php echo htmlspecialchars($summary['high_urgency']); ?></div>
            </div>
        </div>

        <div class="overflow-x-auto mt-4">
            <table class="min-w-full text-sm">
                <thead>
                    <tr style="background:#F5F7FA;color:#2C3E50;">
                        <th class="text-left px-3 py-2">Ticket ID</th>
                        <th class="text-left px-3 py-2">Location</th>
                        <th class="text-left px-3 py-2">Category</th>
                        <th class="text-left px-3 py-2">Urgency</th>
                        <th class="text-left px-3 py-2">Completed Date</th>
                        <th class="text-left px-3 py-2">Duration</th>
                        <th class="text-left px-3 py-2">Notes</th>
                    </tr>
                </thead>
                <tbody style="color:#2C3E50;">
                    <?php $rows = [
                        ['id' => 'R-190', 'loc' => 'Library, Floor 2', 'cat' => 'IT', 'urg' => 'High', 'done' => '2025-11-30 14:10', 'duration' => '1h 20m', 'notes' => 'Replaced cable'],
                        ['id' => 'R-185', 'loc' => 'Block B, Room 5', 'cat' => 'Electrical', 'urg' => 'Medium', 'done' => '2025-11-29 10:20', 'duration' => '2h 05m', 'notes' => 'Reset breaker'],
                        ['id' => 'R-180', 'loc' => 'Block C, Lab 1', 'cat' => 'HVAC', 'urg' => 'Low', 'done' => '2025-11-28 09:00', 'duration' => '3h 10m', 'notes' => 'Cleaned filter'],
                    ]; ?>
                    <?php foreach ($rows as $row): ?>
                        <tr class="border-t" style="border-color:#D7DDE5;">
                            <td class="px-3 py-2 font-semibold" style="color:#1F4E79;"><?php echo htmlspecialchars($row['id']); ?></td>
                            <td class="px-3 py-2"><?php echo htmlspecialchars($row['loc']); ?></td>
                            <td class="px-3 py-2"><?php echo htmlspecialchars($row['cat']); ?></td>
                            <td class="px-3 py-2">
                                <?php
                                $urgColors = [
                                    'high' => ['#E74C3C', '#FFFFFF'],
                                    'medium' => ['#F1C40F', '#2C3E50'],
                                    'low' => ['#2ECC71', '#FFFFFF']
                                ];
                                $u = strtolower($row['urg']);
                                $bg = $urgColors[$u][0] ?? '#D7DDE5';
                                $fg = $urgColors[$u][1] ?? '#2C3E50';
                                ?>
                                <span class="px-2 py-1 rounded-full text-xs font-semibold" style="background:<?php echo $bg; ?>;color:<?php echo $fg; ?>;">
                                    <?php echo htmlspecialchars($row['urg']); ?>
                                </span>
                            </td>
                            <td class="px-3 py-2"><?php echo htmlspecialchars($row['done']); ?></td>
                            <td class="px-3 py-2"><?php echo htmlspecialchars($row['duration']); ?></td>
                            <td class="px-3 py-2" style="color:#7F8C8D;"><?php echo htmlspecialchars($row['notes']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../footer.php'; ?>

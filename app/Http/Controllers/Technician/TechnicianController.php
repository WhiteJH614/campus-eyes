<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TechnicianController extends Controller
{

    public function dashboard()
    {



        $user = Auth::user();

        // Safety: only technicians should see this page
        if (!$user || $user->role !== 'Technician') {
            abort(403);
        }

        $technicianId = $user->id;
        $now = Carbon::now();

        // 1) Stats

        // Assigned = Pending + Assigned for this technician
        $assignedCount = Report::where('technician_id', $technicianId)
            ->whereIn('status', ['Pending', 'Assigned'])
            ->count();

        // In Progress
        $inProgressCount = Report::where('technician_id', $technicianId)
            ->where('status', 'In_Progress')
            ->count();

        // Completed in current month
        $completedThisMonthCount = Report::where('technician_id', $technicianId)
            ->where('status', 'Completed')
            ->whereBetween('completed_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->count();

        // Overdue = due_at < now AND not completed
        $overdueCount = Report::where('technician_id', $technicianId)
            ->where('status', '!=', 'Completed')
            ->whereNotNull('due_at')
            ->where('due_at', '<', $now)
            ->count();

        $stats = [
            ['label' => 'Assigned', 'value' => $assignedCount, 'bg' => '#1F4E79'],
            ['label' => 'In Progress', 'value' => $inProgressCount, 'bg' => '#3498DB'],
            ['label' => 'Completed (month)', 'value' => $completedThisMonthCount, 'bg' => '#27AE60'],
            ['label' => 'Overdue', 'value' => $overdueCount, 'bg' => '#F39C12'],
        ];

        // 2) Next overdue task (for "Next task ->" button)
        $nextOverdue = Report::where('technician_id', $technicianId)
            ->where('status', '!=', 'Completed')
            ->whereNotNull('due_at')
            ->where('due_at', '<', $now)
            ->orderBy('due_at')
            ->first();

        // 3) Recent activity: last 5 updated reports for this technician
        $recentReports = Report::where('technician_id', $technicianId)
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        $recent = $recentReports->map(function (Report $report) {
            $id = $report->id;
            $status = $report->status;
            $updatedAt = $report->updated_at?->format('d M Y H:i');

            return match ($status) {
                'Completed' => "Report #{$id} marked as Completed ({$updatedAt})",
                'In_Progress' => "Report #{$id} status changed to In Progress ({$updatedAt})",
                'Assigned' => "Report #{$id} assigned to you ({$updatedAt})",
                'Pending' => "Report #{$id} is pending assignment ({$updatedAt})",
                default => "Report #{$id} updated ({$updatedAt})",
            };
        })->toArray();

        $stats = [
            ['label' => 'Assigned', 'value' => $assignedCount, 'bg_class' => 'bg-[#1F4E79]'],
            ['label' => 'In Progress', 'value' => $inProgressCount, 'bg_class' => 'bg-[#3498DB]'],
            ['label' => 'Completed (month)', 'value' => $completedThisMonthCount, 'bg_class' => 'bg-[#27AE60]'],
            ['label' => 'Overdue', 'value' => $overdueCount, 'bg_class' => 'bg-[#F39C12]'],
        ];

        return view('Technician.dashboard', [
            'stats' => $stats,
            'recent' => $recent,
            'overdueCount' => $overdueCount,
            'nextOverdue' => $nextOverdue,
        ]);
    }
    /**
     * List jobs assigned to the authenticated technician.
     */
    public function myJobs()
    {
        /** @var User $technician */
        $technician = Auth::user();

        $jobs = $technician->assignedReports()
            ->with(['room.block', 'category'])
            ->orderBy('urgency', 'desc')
            ->get();

        return view('technician.tasks', compact('jobs'));
    }

    /**
     * Show details for a specific job assigned to the technician.
     */
    public function jobDetails($id)
    {
        $job = Report::with(['room.block', 'category', 'attachments'])
            ->where('technician_id', Auth::id())
            ->findOrFail($id);

        return view('technician.job_details', compact('job'));
    }

    /**
     * Update status for a job assigned to the technician.
     */
    public function updateStatus(Request $request, $id)
    {
        $job = Report::where('technician_id', Auth::id())->findOrFail($id);

        $request->validate([
            'status' => 'required|in:Assigned,In_Progress,Completed',
        ]);

        $job->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Task status updated');
    }

    /**
     * Mark a job as completed with resolution notes.
     */
    public function completeJob(Request $request, $id)
    {
        $job = Report::where('technician_id', Auth::id())->findOrFail($id);

        $request->validate([
            'resolution_notes' => 'required|string|max:1000',
        ]);

        $job->update([
            'status' => 'Completed',
            'resolution_notes' => $request->resolution_notes,
            'completed_at' => now(),
        ]);

        return redirect()->route('technician.my_jobs')
            ->with('success', 'Job marked as completed');
    }
}

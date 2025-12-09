<?php

namespace App\Http\Controllers\Technician;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TechnicianController extends Controller
{
    /**
     * 显示所有分配给当前技术员的任务
     */
    public function myJobs()
    {
        $technician = Auth::user();

        $jobs = $technician->assignedReports()
            ->with(['room.block', 'category'])
            ->orderBy('urgency', 'desc')
            ->get();

        return view('technician.my_jobs', compact('jobs'));
    }

    /**
     * 查看任务详细资料
     */
    public function jobDetails($id)
    {
        $job = Report::with(['room.block', 'category', 'attachments'])
            ->where('technician_id', Auth::id())
            ->findOrFail($id);

        return view('technician.job_details', compact('job'));
    }

    /**
     * 更新任务状态（Assigned → In Progress）
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
     * 完成任务 + 填写 resolution notes
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

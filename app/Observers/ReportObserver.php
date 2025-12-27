<?php
// Author: Tan Jun Yan
namespace App\Observers;

use App\Mail\NewReportNotification;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReportObserver
{
    /**
     * Handle the Report "created" event.
     * 
     * Logs when a new maintenance report is submitted and notifies admins.
     */
    public function created(Report $report): void
    {
        // Log the report creation
        Log::channel('daily')->info('New maintenance report created', [
            'report_id' => $report->id,
            'reporter_id' => $report->reporter_id,
            'reporter_name' => $report->reporter?->name,
            'room_id' => $report->room_id,
            'category_id' => $report->category_id,
            'urgency' => $report->urgency,
            'status' => $report->status,
            'description' => substr($report->description, 0, 100) . (strlen($report->description) > 100 ? '...' : ''),
            'created_at' => $report->created_at->toDateTimeString(),
        ]);

        // Send email notification to all admins
        $this->notifyAdmins($report);
    }

    /**
     * Send email notification to all admin users.
     */
    protected function notifyAdmins(Report $report): void
    {
        // Eager load relationships for the email
        $report->load(['reporter', 'room.block', 'category']);

        // Get all admin users
        $admins = User::where('role', 'Admin')->get();

        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(new NewReportNotification($report));
                
                Log::channel('daily')->info('Admin notification sent', [
                    'report_id' => $report->id,
                    'admin_email' => $admin->email,
                ]);
            } catch (\Exception $e) {
                Log::channel('daily')->error('Failed to send admin notification', [
                    'report_id' => $report->id,
                    'admin_email' => $admin->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Handle the Report "updated" event.
     * 
     * Logs when a report status changes.
     */
    public function updated(Report $report): void
    {
        // Log status changes
        if ($report->isDirty('status')) {
            Log::channel('daily')->info('Report status updated', [
                'report_id' => $report->id,
                'old_status' => $report->getOriginal('status'),
                'new_status' => $report->status,
                'technician_id' => $report->technician_id,
                'updated_at' => now()->toDateTimeString(),
            ]);
        }

        // Log technician assignment
        if ($report->isDirty('technician_id') && $report->technician_id !== null) {
            Log::channel('daily')->info('Technician assigned to report', [
                'report_id' => $report->id,
                'technician_id' => $report->technician_id,
                'technician_name' => $report->technician?->name,
                'assigned_at' => now()->toDateTimeString(),
            ]);
        }
    }

    /**
     * Handle the Report "deleted" event.
     */
    public function deleted(Report $report): void
    {
        Log::channel('daily')->warning('Report deleted', [
            'report_id' => $report->id,
            'reporter_id' => $report->reporter_id,
            'deleted_at' => now()->toDateTimeString(),
        ]);
    }
}

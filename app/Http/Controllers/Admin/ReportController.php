<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with(['technician', 'room.block', 'category']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->urgency) {
            $query->where('urgency', $request->urgency);
        }

        if ($request->search) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $reports = $query->orderBy('created_at', 'desc')->get();

        return view('admin.reports.index', compact('reports'));
    }

    public function show(Report $report)
    {
        $technicians = User::technicians()->get();
        return view('admin.reports.show', compact('report', 'technicians'));
    }

    public function assignTechnician(Request $request, Report $report)
    {
        $request->validate([
            'technician_id' => 'required|exists:users,id',
        ]);

        $report->update([
            'technician_id' => $request->technician_id,
            'status' => 'Assigned',
        ]);

        return redirect()->back();
    }
}

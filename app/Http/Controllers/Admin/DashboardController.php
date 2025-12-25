<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalReports = Report::count();
        $unassignedReports = Report::whereNull('technician_id')->count();
        $completedReports = Report::where('status', 'Completed')->count();

        $faultsByBuilding = DB::table('reports')
            ->join('rooms', 'reports.room_id', '=', 'rooms.id')
            ->join('blocks', 'rooms.block_id', '=', 'blocks.id')
            ->select('blocks.block_name', DB::raw('COUNT(*) as total'))
            ->groupBy('blocks.block_name')
            ->get();

        return view('admin.dashboard', compact(
            'totalReports',
            'unassignedReports',
            'completedReports',
            'faultsByBuilding'
        ));
    }
}

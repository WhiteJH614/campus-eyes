<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportApiController extends Controller
{
    /**
     * Get all reports with optional filters.
     * 
     * GET /api/v1/reports
     * 
     * Query Parameters:
     * - status: Filter by status (Pending, Assigned, In_Progress, Completed)
     * - urgency: Filter by urgency (Low, Medium, High)
     * - category_id: Filter by category
     * - block_id: Filter by block
     * - per_page: Number of results per page (default: 15)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Report::with(['reporter:id,name,email', 'room.block', 'category', 'technician:id,name']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('urgency')) {
            $query->where('urgency', $request->urgency);
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('block_id')) {
            $query->whereHas('room', function ($q) use ($request) {
                $q->where('block_id', $request->block_id);
            });
        }

        $perPage = $request->get('per_page', 15);
        $reports = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $reports->items(),
            'meta' => [
                'current_page' => $reports->currentPage(),
                'last_page' => $reports->lastPage(),
                'per_page' => $reports->perPage(),
                'total' => $reports->total(),
            ]
        ]);
    }

    /**
     * Get a specific report by ID.
     * 
     * GET /api/v1/reports/{id}
     */
    public function show(int $id): JsonResponse
    {
        $report = Report::with([
            'reporter:id,name,email,phone_number',
            'room.block',
            'category',
            'technician:id,name,email,specialization',
            'attachments'
        ])->find($id);

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Report not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Get report statistics.
     * 
     * GET /api/v1/reports/stats
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total' => Report::count(),
            'by_status' => [
                'pending' => Report::where('status', 'Pending')->count(),
                'assigned' => Report::where('status', 'Assigned')->count(),
                'in_progress' => Report::where('status', 'In_Progress')->count(),
                'completed' => Report::where('status', 'Completed')->count(),
            ],
            'by_urgency' => [
                'low' => Report::where('urgency', 'Low')->count(),
                'medium' => Report::where('urgency', 'Medium')->count(),
                'high' => Report::where('urgency', 'High')->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}

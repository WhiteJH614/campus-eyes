<?php
// Author: Tan Jun Yan
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Report API Controller
 * 
 * Exposes RESTful web services for the Reporter Module.
 * Compliant with Interface Agreement (IFA) standards.
 * 
 * @package App\Http\Controllers\Api
 */
class ReportApiController extends Controller
{
    /**
     * Generate a standardized API response following IFA standards.
     * 
     * @param string $status Response status (success, error, not_found)
     * @param mixed $data Response data
     * @param string|null $message Optional message
     * @param int $httpCode HTTP status code
     * @param string|null $requestId Request tracking ID
     * @return JsonResponse
     */
    protected function apiResponse(
        string $status,
        mixed $data = null,
        ?string $message = null,
        int $httpCode = 200,
        ?string $requestId = null
    ): JsonResponse {
        $requestId = $requestId ?? Str::uuid()->toString();
        $timestamp = now()->toIso8601String();

        $response = [
            'status' => $status,
            'timestamp' => $timestamp,
            'requestId' => $requestId,
        ];

        if ($message !== null) {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        // Log the API request/response
        \Illuminate\Support\Facades\Log::channel('daily')->info('API Request', [
            'requestId' => $requestId,
            'status' => $status,
            'httpCode' => $httpCode,
            'message' => $message,
            'timestamp' => $timestamp,
        ]);

        return response()->json($response, $httpCode);
    }

    /**
     * Get all reports with optional filters.
     * 
     * GET /api/v1/reports
     * 
     * IFA Function: getReports
     * Source Module: Reporter Module
     * Target Modules: Technician Module, Admin Module
     * 
     * Query Parameters:
     * - requestId: (Optional) Unique request tracking ID
     * - timestamp: (Optional) Request timestamp
     * - status: Filter by status (Pending, Assigned, In_Progress, Completed)
     * - urgency: Filter by urgency (Low, Medium, High)
     * - category_id: Filter by category
     * - block_id: Filter by block
     * - per_page: Number of results per page (default: 15)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Get or generate request tracking ID
        $requestId = $request->get('requestId', Str::uuid()->toString());

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

        return $this->apiResponse(
            status: 'success',
            data: [
                'reports' => $reports->items(),
                'pagination' => [
                    'current_page' => $reports->currentPage(),
                    'last_page' => $reports->lastPage(),
                    'per_page' => $reports->perPage(),
                    'total' => $reports->total(),
                ]
            ],
            message: 'Reports retrieved successfully',
            requestId: $requestId
        );
    }

    /**
     * Get a specific report by ID.
     * 
     * GET /api/v1/reports/{id}
     * 
     * IFA Function: getReportById
     * Source Module: Reporter Module
     * Target Modules: Technician Module, Admin Module
     * 
     * @param Request $request
     * @param int $id Report ID
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $requestId = $request->get('requestId', Str::uuid()->toString());

        $report = Report::with([
            'reporter:id,name,email,phone_number',
            'room.block',
            'category',
            'technician:id,name,email,specialization',
            'attachments'
        ])->find($id);

        if (!$report) {
            return $this->apiResponse(
                status: 'not_found',
                message: 'Report not found',
                httpCode: 404,
                requestId: $requestId
            );
        }

        return $this->apiResponse(
            status: 'success',
            data: ['report' => $report],
            message: 'Report retrieved successfully',
            requestId: $requestId
        );
    }

    /**
     * Get report statistics.
     * 
     * GET /api/v1/reports/stats
     * 
     * IFA Function: getReportStatistics
     * Source Module: Reporter Module
     * Target Modules: Admin Module, Analytics Module
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function stats(Request $request): JsonResponse
    {
        $requestId = $request->get('requestId', Str::uuid()->toString());

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

        return $this->apiResponse(
            status: 'success',
            data: ['statistics' => $stats],
            message: 'Statistics retrieved successfully',
            requestId: $requestId
        );
    }

    /**
     * Get reports by status.
     * 
     * GET /api/v1/reports/status/{status}
     * 
     * IFA Function: getReportsByStatus
     * Source Module: Reporter Module
     * Target Modules: Technician Module
     * 
     * @param Request $request
     * @param string $status Report status
     * @return JsonResponse
     */
    public function byStatus(Request $request, string $status): JsonResponse
    {
        $requestId = $request->get('requestId', Str::uuid()->toString());

        $validStatuses = ['Pending', 'Assigned', 'In_Progress', 'Completed'];
        
        if (!in_array($status, $validStatuses)) {
            return $this->apiResponse(
                status: 'error',
                message: 'Invalid status. Valid values: ' . implode(', ', $validStatuses),
                httpCode: 400,
                requestId: $requestId
            );
        }

        $reports = Report::with(['reporter:id,name', 'room.block', 'category'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->apiResponse(
            status: 'success',
            data: [
                'status_filter' => $status,
                'count' => $reports->count(),
                'reports' => $reports
            ],
            message: "Reports with status '{$status}' retrieved successfully",
            requestId: $requestId
        );
    }

    /**
     * Get reports by urgency level.
     * 
     * GET /api/v1/reports/urgency/{urgency}
     * 
     * IFA Function: getReportsByUrgency
     * Source Module: Reporter Module
     * Target Modules: Technician Module, Admin Module
     * 
     * @param Request $request
     * @param string $urgency Urgency level
     * @return JsonResponse
     */
    public function byUrgency(Request $request, string $urgency): JsonResponse
    {
        $requestId = $request->get('requestId', Str::uuid()->toString());

        $validUrgencies = ['Low', 'Medium', 'High'];
        
        if (!in_array($urgency, $validUrgencies)) {
            return $this->apiResponse(
                status: 'error',
                message: 'Invalid urgency. Valid values: ' . implode(', ', $validUrgencies),
                httpCode: 400,
                requestId: $requestId
            );
        }

        $reports = Report::with(['reporter:id,name', 'room.block', 'category'])
            ->where('urgency', $urgency)
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->apiResponse(
            status: 'success',
            data: [
                'urgency_filter' => $urgency,
                'count' => $reports->count(),
                'reports' => $reports
            ],
            message: "Reports with urgency '{$urgency}' retrieved successfully",
            requestId: $requestId
        );
    }
}

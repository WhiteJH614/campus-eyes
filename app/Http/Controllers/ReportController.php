<?php
// Author: Tan Jun Yan
namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Block;
use App\Models\Category;
use App\Models\Report;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Class ReportController
 * 
 * Controller for managing reports by the reporter.
 * 
 * @author Tan Jun Yan
 */
class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // Get status counts for the current user
        $statusCounts = Report::where('reporter_id', Auth::id())
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $sortColumn = $request->query('sort_by', 'created_at');
        $sortDirection = $request->query('sort_direction', 'desc');
        $filterStatus = $request->query('status');

        $allowedSorts = ['id', 'location', 'category', 'status', 'created_at'];

        if (!in_array($sortColumn, $allowedSorts)) {
            $sortColumn = 'created_at';
        }

        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        $query = Report::where('reporter_id', Auth::id())
            ->with(['room.block', 'category', 'technician']);

        // Apply status filter if present
        if ($filterStatus && in_array($filterStatus, ['Pending', 'In_Progress', 'Completed', 'Assigned'])) {
            $query->where('status', $filterStatus);
        }

        switch ($sortColumn) {
            case 'location':
                $query->join('rooms', 'reports.room_id', '=', 'rooms.id')
                      ->join('blocks', 'rooms.block_id', '=', 'blocks.id')
                      ->select('reports.*')
                      ->orderBy('blocks.block_name', $sortDirection)
                      ->orderBy('rooms.room_name', $sortDirection);
                break;
            case 'category':
                $query->join('categories', 'reports.category_id', '=', 'categories.id')
                      ->select('reports.*')
                      ->orderBy('categories.name', $sortDirection);
                break;
            default:
                $query->select('reports.*')->orderBy('reports.' . $sortColumn, $sortDirection);
                break;
        }

        $reports = $query->paginate(10)->withQueryString();

        return view('reports.index', compact('reports', 'statusCounts'));
    }

    /**
     * Show the form for creating a new report.
     */
    public function create(): View
    {
        $blocks = Block::all();
        $categories = Category::all();

        return view('reports.create', compact('blocks', 'categories'));
    }

    /**
     * Store a newly created report in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'string', 'max:2000'],
            'urgency' => ['required', 'in:Low,Medium,High'],
            'attachment' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'], // 5MB max
        ]);

        // Create the report
        $report = Report::create([
            'reporter_id' => Auth::id(),
            'room_id' => $validated['room_id'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],
            'urgency' => $validated['urgency'],
            'status' => 'Pending',
        ]);

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('attachments', $fileName, 'public');

            Attachment::create([
                'report_id' => $report->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_type' => $file->getClientMimeType(),
                'attachment_type' => 'REPORTER_PROOF',
            ]);
        }

        return redirect()->route('reports.index')
            ->with('success', 'Report submitted successfully! Your report ID is #' . $report->id);
    }

    /**
     * Display the specified report.
     */
    public function show(Report $report): View
    {
        // Ensure user can only view their own reports
        if ($report->reporter_id !== Auth::id()) {
            abort(403, 'You can only view your own reports.');
        }

        $report->load(['room.block', 'category', 'technician', 'attachments']);

        return view('reports.show', compact('report'));
    }

    /**
     * Show the form for editing the specified report.
     */
    public function edit(Report $report): View
    {
        if ($report->reporter_id !== Auth::id()) {
            abort(403, 'You can only edit your own reports.');
        }

        if ($report->status !== 'Pending') {
            abort(403, 'You can only edit pending reports.');
        }

        $blocks = Block::all();
        $categories = Category::all();

        return view('reports.edit', compact('report', 'blocks', 'categories'));
    }

    /**
     * Update the specified report in storage.
     */
    public function update(Request $request, Report $report): RedirectResponse
    {
        if ($report->reporter_id !== Auth::id()) {
            abort(403);
        }

        if ($report->status !== 'Pending') {
            return redirect()->route('reports.show', $report)
                ->with('error', 'Cannot update a report that is already being processed.');
        }

        $validated = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'string', 'max:2000'],
            'urgency' => ['required', 'in:Low,Medium,High'],
            'attachment' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $report->update([
            'room_id' => $validated['room_id'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],
            'urgency' => $validated['urgency'],
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('attachments', $fileName, 'public');

            Attachment::create([
                'report_id' => $report->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_type' => $file->getClientMimeType(),
                'attachment_type' => 'REPORTER_PROOF',
            ]);
        }

        return redirect()->route('reports.show', $report)
            ->with('success', 'Report updated successfully.');
    }

    /**
     * Remove the specified report from storage.
     */
    public function destroy(Report $report): RedirectResponse
    {
        if ($report->reporter_id !== Auth::id()) {
            abort(403);
        }

        if ($report->status !== 'Pending') {
            return redirect()->route('reports.show', $report)
                ->with('error', 'Cannot delete a report that is already being processed.');
        }

        // Delete attachments
        foreach ($report->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
        }

        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', 'Report deleted successfully.');
    }

    /**
     * Get rooms by block (AJAX endpoint).
     */
    public function getRoomsByBlock(int $blockId)
    {
        $block = Block::where('id', $blockId)->firstOrFail();
        $rooms = $block->rooms()->orderBy('floor_number')->orderBy('room_name')->get();

        return response()->json($rooms);
    }
}

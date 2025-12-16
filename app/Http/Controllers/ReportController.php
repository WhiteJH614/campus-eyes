<?php

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
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display a listing of the user's reports.
     */
    public function index(): View
    {
        $reports = Report::where('reporter_id', Auth::id())
            ->with(['room.block', 'category', 'technician'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('reports.index', compact('reports'));
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
            'room_id' => ['required', 'exists:rooms,room_id'],
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
     * Get rooms by block (AJAX endpoint).
     */
    public function getRoomsByBlock(int $blockId)
    {
        $block = Block::where('block_id', $blockId)->firstOrFail();
        $rooms = $block->rooms()->orderBy('floor_number')->orderBy('room_name')->get();

        return response()->json($rooms);
    }
}

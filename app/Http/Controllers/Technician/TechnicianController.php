<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Report;
use App\Models\User;
use App\Factories\SortStrategyFactory;
use App\Models\Attachment;
use App\Services\TechnicianJobView;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TechnicianController extends Controller
{
    /**
     * Serve the dashboard blade; data loads via dashboardApi.
     */
    public function dashboard()
    {
        return view('Technician.dashboard');
    }

    /**
     * Serve the tasks blade; data loads via tasksApi.
     */
    public function tasks()
    {
        return view('Technician.tasks');
    }

    /**
     * Serve the completed blade; data loads via completedApi.
     */
    public function completed()
    {
        return view('Technician.completed');
    }

    /**
     * Serve the task detail blade; data loads via taskDetailApi.
     */
    public function taskDetail($id)
    {
        return view('Technician.task-detail');
    }

    /**
     * Serve the profile blade; data loads via profileApi.
     */
    public function profile()
    {
        return view('Technician.profile');
    }

    public function dashboardApi()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['status' => 401, 'message' => 'Unauthenticated'], 401);
        }

        if ($user->role !== 'Technician') {
            return response()->json(['status' => 403, 'message' => 'Forbidden'], 403);
        }

        $data = $this->getDashboardData($user->id);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => [
                'stats' => $data['stats_raw'],
                'overdueCount' => $data['overdueCount'],
                'nextOverdue' => $data['nextOverdue'],
                'recent' => $data['recent'],
            ],
        ]);
    }

    private function getDashboardData(int $technicianId): array
    {
        $now = Carbon::now('Asia/Kuala_Lumpur');
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $assignedCount = Report::where('technician_id', $technicianId)
            ->where('status', 'Assigned')
            ->count();

        $inProgressCount = Report::where('technician_id', $technicianId)
            ->where('status', 'In_Progress')
            ->count();

        $completedThisMonthCount = Report::where('technician_id', $technicianId)
            ->where('status', 'Completed')
            ->whereBetween('completed_at', [$startOfMonth, $endOfMonth])
            ->count();

        $overdueCount = Report::where('technician_id', $technicianId)
            ->where('status', '!=', 'Completed')
            ->whereRaw('COALESCE(due_at, created_at) < ?', [$now])
            ->count();

        $nextOverdue = Report::where('technician_id', $technicianId)
            ->where('status', '!=', 'Completed')
            ->whereRaw('COALESCE(due_at, created_at) < ?', [$now])
            ->orderByRaw('COALESCE(due_at, created_at)')
            ->first();

        $recent = Report::where('technician_id', $technicianId)
            ->orderByDesc('updated_at')
            ->take(5)
            ->get()
            ->map(function (Report $report) {
                $time = optional($report->updated_at)->format('d M Y H:i');

                return match ($report->status) {
                    'Completed' => "Report {$report->id}\nmarked as Completed ({$time})",
                    'In_Progress' => "Report {$report->id}\nstatus changed to In Progress ({$time})",
                    'Assigned' => "Report {$report->id}\nassigned to you ({$time})",
                    default => "Report {$report->id}\nupdated ({$time})",
                };
            })->values()->toArray();

        return [
            'stats_raw' => [
                ['label' => 'Assigned', 'value' => $assignedCount],
                ['label' => 'In Progress', 'value' => $inProgressCount],
                ['label' => 'Completed (month)', 'value' => $completedThisMonthCount],
                ['label' => 'Overdue', 'value' => $overdueCount],
            ],
            'overdueCount' => $overdueCount,
            'nextOverdue' => $nextOverdue ? [
                'id' => $nextOverdue->id,
                'due_at' => $nextOverdue->due_at,
                'status' => $nextOverdue->status,
            ] : null,
            'recent' => $recent,
        ];
    }

    public function tasksApi(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['status' => 401], 401);
        }

        if ($user->role !== 'Technician') {
            return response()->json(['status' => 403], 403);
        }

        $now = Carbon::now('Asia/Kuala_Lumpur');

        $query = Report::with(['room.block.campus', 'category'])
            ->where('technician_id', $user->id)
            ->whereIn('status', ['Assigned', 'In_Progress']);

        if ($request->filled('q')) {
            $query->where('id', 'like', '%' . trim($request->q) . '%');
        }

        if ($request->filled('status')) {
            if ($request->status === 'Overdue') {
                $query->where('status', '!=', 'Completed')
                    ->whereNotNull('due_at')
                    ->where('due_at', '<', $now);
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('urgency')) {
            $query->where('urgency', $request->urgency);
        }

        $sortKey = $request->query('sort', 'due');

        $reports = $query->get();
        $jobView = new TechnicianJobView(SortStrategyFactory::make($sortKey));
        $sorted = $jobView->getSortedReports($reports);

        $perPage = 10;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $items = $sorted->slice(($page - 1) * $perPage, $perPage)->values();
        $paginator = new LengthAwarePaginator($items, $sorted->count(), $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        $jobs = $paginator->map(function (Report $job) use ($now) {
            $due = $job->due_at;
            $isOverdue = ($job->status === 'Overdue') || ($due && $due->lt($now) && $job->status !== 'Completed');
            $overdueHuman = $isOverdue && $due ? $due->diffForHumans($now, true) : null;
            $location = trim(collect([
                optional(optional($job->room)->block)->campus->campus_name ?? '',
                optional($job->room->block ?? null)->block_name ?? '',
                optional($job->room)->room_name ?? '',
            ])->filter()->implode(', '));
            $blockName = optional($job->room->block ?? null)->block_name ?? '-';
            $roomName = optional($job->room)->room_name ?? '-';

            return [
                'id' => $job->id,
                'reported_at' => optional($job->created_at)?->format('d M Y H:i') ?? '-',
                'location' => $location,
                'block_name' => $blockName,
                'room_name' => $roomName,
                'category' => optional($job->category)->name ?? '-',
                'urgency' => $job->urgency ?? '-',
                'status' => $job->status ?? '-',
                'due_at' => $job->due_at?->format('d M Y H:i') ?? '-',
                'is_overdue' => $isOverdue,
                'overdue_human' => $overdueHuman,
            ];
        });

        return response()->json([
            'status' => 200,
            'data' => [
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'total' => $paginator->total(),
                ],
                'jobs' => $jobs,
            ],
        ]);
    }

    public function completedApi(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => 401], 401);
        }
        if ($user->role !== 'Technician') {
            return response()->json(['status' => 403], 403);
        }

        $query = Report::with(['room.block', 'category'])
            ->where('technician_id', $user->id)
            ->where('status', 'Completed');

        if ($request->filled('from')) {
            $query->whereDate('completed_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('completed_at', '<=', $request->to);
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('block')) {
            $query->whereHas('room.block', function ($q) use ($request) {
                $q->where('block_name', $request->block);
            });
        }
        if ($request->filled('q')) {
            $query->where('resolution_notes', 'like', '%' . $request->q . '%');
        }

        $range = (int) $request->input('range', 0);
        if ($range > 0) {
            $query->where('completed_at', '>=', now()->subDays($range));
        }

        $sortKey = $request->query('sort', 'due');
        $reportsCollection = $query->get();
        $jobView = new TechnicianJobView(SortStrategyFactory::make($sortKey));
        $sortedCompleted = $jobView->getSortedReports($reportsCollection);

        $perPage = 15;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $paginated = new LengthAwarePaginator(
            $sortedCompleted->slice(($page - 1) * $perPage, $perPage)->values(),
            $sortedCompleted->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $rows = $paginated->map(function (Report $r) {
            $loc = optional($r->room);
            $block = optional($loc?->block);
            $start = $r->assigned_at ?? $r->created_at;
            $end = $r->completed_at;
            $duration = ($start && $end) ? $start->diffForHumans($end, true) : '-';

            return [
                'id' => $r->id,
                'reporter_id' => $r->reporter_id,
                'room' => $loc?->room_name ?? '-',
                'block' => $block?->block_name ?? '-',
                'category' => optional($r->category)->name ?? '-',
                'description' => $r->description ?? '-',
                'urgency' => $r->urgency ?? '-',
                'status' => $r->status ?? '-',
                'resolution_notes' => $r->resolution_notes ?? '-',
                'report_at' => optional($r->created_at)?->format('Y-m-d H:i') ?? '-',
                'due_at' => optional($r->due_at)?->format('Y-m-d H:i') ?? '-',
                'completed_at' => optional($r->completed_at)?->format('Y-m-d H:i') ?? '-',
                'duration' => $duration,
            ];
        })->toArray();

        $total = $paginated->total();
        $highUrgency = $reportsCollection->where('urgency', 'High')->count();
        $avgSeconds = $reportsCollection->map(function ($r) {
            $start = $r->assigned_at ?? $r->created_at;
            return ($start && $r->completed_at) ? $start->diffInSeconds($r->completed_at) : null;
        })->filter()->avg();
        $avgTime = $avgSeconds
            ? sprintf('%dh %02dm', floor($avgSeconds / 3600), floor($avgSeconds % 3600 / 60))
            : '-';

        $categories = Category::orderBy('name')->get(['id', 'name']);

        return response()->json([
            'status' => 200,
            'data' => [
                'summary' => [
                    'total' => $total,
                    'avg_time' => $avgTime,
                    'high_urgency' => $highUrgency,
                ],
                'rows' => $rows,
                'pagination' => [
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                    'total' => $paginated->total(),
                ],
                'categories' => $categories,
            ],
        ]);
    }

    public function profileApi()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => 401], 401);
        }
        if ($user->role !== 'Technician') {
            return response()->json(['status' => 403], 403);
        }

        return response()->json([
            'status' => 200,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'campus' => $user->campus,
                'specialization' => $user->specialization,
                'availability_status' => $user->availability_status,
                'updated_at' => optional($user->updated_at)?->toIso8601String(),
            ],
        ]);
    }

    public function profileUpdateApi(Request $request)
    {
        $authUser = Auth::user();
        if (!$authUser) {
            return response()->json(['status' => 401], 401);
        }
        if ($authUser->role !== 'Technician') {
            return response()->json(['status' => 403], 403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $authUser->id],
            'phone_number_digits' => ['nullable', 'regex:/^[0-9]{9,11}$/'],
            'campus' => ['nullable', Rule::in(['Penang'])],
            'specialization' => ['nullable', 'array'],
            'specialization.*' => [
                Rule::in([
                    'Electrical',
                    'Networking',
                    'AirConditioning',
                    'Plumbing',
                    'Carpentry',
                    'AudioVisual',
                    'Landscaping',
                    'Security',
                    'Cleaning',
                ])
            ],
            'availability_status' => ['nullable', 'in:Available,Busy,On_Leave'],
        ]);

        $phoneDigits = $data['phone_number_digits'] ?? null;
        if ($phoneDigits !== null && $phoneDigits !== '') {
            if (str_starts_with($phoneDigits, '0')) {
                $phoneDigits = substr($phoneDigits, 1);
            }

            if (strlen($phoneDigits) < 8 || strlen($phoneDigits) > 10) {
                return response()->json([
                    'status' => 422,
                    'errors' => ['phone_number_digits' => ['Phone number must be 8-10 digits after the +60 prefix.']],
                ], 422);
            }

            $data['phone_number'] = '+60' . $phoneDigits;
        } else {
            $data['phone_number'] = null;
        }
        unset($data['phone_number_digits']);

        $specializationValues = $data['specialization'] ?? [];
        $data['specialization'] = $specializationValues ? implode(',', $specializationValues) : null;

        /** @var \App\Models\User $userModel */
        $userModel = User::findOrFail($authUser->id);
        $userModel->fill($data);
        $userModel->save();

        return response()->json(['status' => 200, 'message' => 'Profile updated.']);
    }

    public function profilePasswordApi(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => 401], 401);
        }
        if ($user->role !== 'Technician') {
            return response()->json(['status' => 403], 403);
        }

        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => 422,
                'errors' => ['current_password' => ['Current password is incorrect.']],
            ], 422);
        }

        /** @var \App\Models\User $userModel */
        $userModel = User::findOrFail($user->id);
        $userModel->password = Hash::make($request->new_password);
        $userModel->save();

        return response()->json(['status' => 200, 'message' => 'Password updated.']);
    }

    /**
     * Handle status updates from the task detail form (including reject to Pending).
     */
    public function updateStatus(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Technician') {
            abort(403);
        }

        $data = $request->validate([
            'status' => ['required', 'in:In_Progress,Pending'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $job = Report::where('technician_id', $user->id)->findOrFail($id);
        if ($job->status === 'Completed') {
            return back()->withErrors(['status' => 'Task is already completed and cannot be changed.']);
        }

        if ($data['status'] === 'Pending' && empty($data['reason'])) {
            return back()->withErrors(['reason' => 'Reason is required when rejecting to Pending.']);
        }

        $job->status = $data['status'];
        if ($data['status'] === 'Pending') {
            $job->technician_id = null;
        }

        if ($data['status'] === 'Pending' && !empty($data['reason'])) {
            $job->resolution_notes = '[Rejected] ' . trim($data['reason']);
        }

        $job->save();

        if ($data['status'] === 'Pending') {
            return redirect()->route('technician.tasks')->with('success', 'Task rejected and sent back to Pending.');
        }

        return back()->with('success', 'Status updated.');
    }

    /**
     * Complete job with proof upload (web form).
     */
    public function completeJob(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Technician') {
            abort(403);
        }

        $request->validate([
            'resolution_notes' => ['required', 'string', 'max:2000'],
            'proof_images' => ['required', 'array', 'min:1'],
            'proof_images.*' => ['file', 'image', 'max:5120'],
        ]);

        $job = Report::where('technician_id', $user->id)->findOrFail($id);
        if ($job->status !== 'In_Progress') {
            return back()->withErrors(['status' => 'Set the task to In Progress before completing it.']);
        }

        $job->status = 'Completed';
        $job->resolution_notes = $request->resolution_notes;
        $job->completed_at = Carbon::now();
        $job->save();

        foreach ($request->file('proof_images', []) as $img) {
            $storedPath = $img->store('attachments/technician', 'public');

            Attachment::create([
                'report_id' => $job->id,
                'file_name' => $img->getClientOriginalName(),
                'file_path' => $storedPath,
                'file_type' => $img->getClientMimeType(),
                'attachment_type' => 'TECHNICIAN_PROOF',
                'uploaded_at' => Carbon::now('Asia/Kuala_Lumpur'),
            ]);
        }

        return redirect()
            ->route('technician.task_detail', $job->id)
            ->with('success', 'Task completed with proof images uploaded.');
    }

    public function deleteAfterPhoto(int $id, int $attachmentId)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Technician') {
            abort(403);
        }

        $job = Report::where('technician_id', $user->id)->findOrFail($id);

        $attachment = Attachment::where('id', $attachmentId)
            ->where('report_id', $job->id)
            ->where('attachment_type', 'TECHNICIAN_PROOF')
            ->firstOrFail();

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return back()->with('success', 'Photo removed.');
    }

    /**
     * Upload additional technician proof images without changing status/notes.
     */
    public function addProofImages(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Technician') {
            abort(403);
        }

        $request->validate([
            'proof_images' => ['required', 'array', 'min:1'],
            'proof_images.*' => ['file', 'image', 'max:5120'],
        ]);

        $job = Report::where('technician_id', $user->id)->findOrFail($id);
        if (!in_array($job->status, ['In_Progress', 'Completed'], true)) {
            return back()->withErrors(['status' => 'Add proof images only when task is In Progress or Completed.']);
        }

        foreach ($request->file('proof_images', []) as $img) {
            $storedPath = $img->store('attachments/technician', 'public');

            Attachment::create([
                'report_id' => $job->id,
                'file_name' => $img->getClientOriginalName(),
                'file_path' => $storedPath,
                'file_type' => $img->getClientMimeType(),
                'attachment_type' => 'TECHNICIAN_PROOF',
                'uploaded_at' => Carbon::now('Asia/Kuala_Lumpur'),
            ]);
        }

        return back()->with('success', 'Proof images uploaded.');
    }

    public function taskDetailApi($id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['status' => 401, 'message' => 'Unauthenticated', 'data' => null], 401);
        }

        if ($user->role !== 'Technician') {
            return response()->json(['status' => 403, 'message' => 'Forbidden (Technician only)', 'data' => null], 403);
        }

        $job = Report::with(['room.block.campus', 'category', 'attachments'])
            ->where('technician_id', $user->id)
            ->findOrFail($id);

        $beforeAttachment = $job->attachments->firstWhere('attachment_type', 'REPORTER_PROOF');
        $afterAttachments = $job->attachments
            ->where('attachment_type', 'TECHNICIAN_PROOF')
            ->map(fn($a) => [
                'id' => $a->id,
                'url' => asset('storage/' . $a->file_path),
                'uploaded_at' => $a->uploaded_at,
            ])->values();

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => [
                'id' => $job->id,
                'description' => $job->description,
                'urgency' => $job->urgency,
                'status_value' => $job->status,
                'reported_at' => optional($job->created_at)->toIso8601String(),
                'assigned_at' => optional($job->assigned_at)->toIso8601String(),
                'due_at' => optional($job->due_at)->toIso8601String(),
                'completed_at' => optional($job->completed_at)->toIso8601String(),
                'location' => [
                    'campus' => optional(optional(optional($job->room)->block)->campus)->campus_name,
                    'block' => optional($job->room->block ?? null)->block_name,
                    'room' => optional($job->room)->room_name,
                ],
                'category' => optional($job->category)->name,
                'attachments' => [
                    'reporter_proof' => $beforeAttachment
                        ? [
                            'url' => asset('storage/' . $beforeAttachment->file_path),
                            'uploaded_at' => $beforeAttachment->uploaded_at,
                        ]
                        : null,
                    'technician_proofs' => $afterAttachments,
                ],
            ],
        ], 200);
    }
}

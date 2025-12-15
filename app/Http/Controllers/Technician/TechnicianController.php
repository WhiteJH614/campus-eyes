<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use App\Models\Attachment;
use App\Factories\SortStrategyFactory;
use App\Services\TechnicianJobView;

class TechnicianController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'Technician') {
            abort(403);
        }

        $data = $this->getDashboardData($user->id);

        $colorMap = [
            'Assigned' => 'bg-[#1F4E79]',
            'In Progress' => 'bg-[#3498DB]',
            'Completed (month)' => 'bg-[#27AE60]',
            'Overdue' => 'bg-[#F39C12]',
        ];

        $stats = collect($data['stats_raw'])->map(function ($s) use ($colorMap) {
            $label = $s['label'] ?? 'Unknown';

            return [
                'label' => $label,
                'value' => $s['value'] ?? 0,
                'bg_class' => $colorMap[$label] ?? 'bg-[#2C3E50]',
            ];
        })->toArray();

        $nextOverdue = null;
        if (!empty($data['nextOverdue']['id'])) {
            $nextOverdue = (object) ['id' => $data['nextOverdue']['id']];
        }

        return view('Technician.dashboard', [
            'stats' => $stats,
            'recent' => $data['recent'],
            'overdueCount' => $data['overdueCount'],
            'nextOverdue' => $nextOverdue,
        ]);
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
        $now = Carbon::now();

        $assignedCount = Report::where('technician_id', $technicianId)
            ->where('status', 'Assigned')
            ->count();

        $inProgressCount = Report::where('technician_id', $technicianId)
            ->where('status', 'In_Progress')
            ->count();

        $completedThisMonthCount = Report::where('technician_id', $technicianId)
            ->where('status', 'Completed')
            ->whereBetween('completed_at', [$now->startOfMonth(), $now->endOfMonth()])
            ->count();

        $overdueCount = Report::where('technician_id', $technicianId)
            ->where('status', '!=', 'Completed')
            ->whereNotNull('due_at')
            ->where('due_at', '<', $now)
            ->count();

        $nextOverdue = Report::where('technician_id', $technicianId)
            ->where('status', '!=', 'Completed')
            ->whereNotNull('due_at')
            ->where('due_at', '<', $now)
            ->orderBy('due_at')
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

    public function tasks(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'Technician') {
            abort(403);
        }

        $query = Report::with(['room.block.campus', 'category'])
            ->where('technician_id', $user->id)
            ->whereIn('status', ['Assigned', 'In_Progress']);

        if ($request->filled('q')) {
            $query->where('id', 'like', '%' . trim($request->q) . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
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

        return view('Technician.tasks', [
            'jobs' => $paginator,
            'filters' => $request->only(['q', 'status', 'urgency', 'sort']),
        ]);
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

        $query = Report::with(['room.block.campus', 'category'])
            ->where('technician_id', $user->id)
            ->whereIn('status', ['Assigned', 'In_Progress']);

        if ($request->filled('q')) {
            $query->where('id', 'like', '%' . trim($request->q) . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('urgency')) {
            $query->where('urgency', $request->urgency);
        }

        if ($request->sort === 'due') {
            $query->orderBy('due_at');
        } elseif ($request->sort === 'urgency') {
            $query->orderByRaw("FIELD(urgency,'High','Medium','Low')");
        } else {
            $query->orderByDesc('created_at');
        }

        $jobs = $query->paginate(10)->withQueryString();

        return response()->json([
            'status' => 200,
            'data' => [
                'pagination' => [
                    'current_page' => $jobs->currentPage(),
                    'last_page' => $jobs->lastPage(),
                    'total' => $jobs->total(),
                ],
                'jobs' => $jobs->items(),
            ],
        ]);
    }

    public function completed(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Technician') {
            abort(403);
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
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
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
            $start = $r->assigned_at ?? $r->created_at;
            $end = $r->completed_at;
            $duration = ($start && $end) ? $start->diffForHumans($end, true) : '-';
            $loc = trim(
                collect([
                    optional(optional($r->room)->block)->block_name,
                    optional($r->room)->room_name,
                ])->filter()->implode(', ')
            );
            return [
                'report_id' => $r->id,
                'id' => 'R-' . $r->id,
                'loc' => $loc ?: '-',
                'cat' => optional($r->category)->name ?: '-',
                'urg' => $r->urgency ?? '-',
                'done' => optional($r->completed_at)?->format('Y-m-d H:i') ?? '-',
                'due_at' => optional($r->due_at)?->format('Y-m-d H:i') ?? '-',
                'status' => $r->status ?? '-',
                'duration' => $duration,
                'notes' => $r->resolution_notes ?? '-',
            ];
        })->toArray();

        $total = $reports->total();
        $highUrgency = $reports->where('urgency', 'High')->count();
        $avgSeconds = $reports->filter(fn($r) => $r->assigned_at && $r->completed_at)
            ->map(fn($r) => $r->assigned_at->diffInSeconds($r->completed_at))
            ->avg();
        $avgTime = $avgSeconds
            ? sprintf('%dh %02dm', floor($avgSeconds / 3600), floor($avgSeconds % 3600 / 60))
            : '-';

        return view('Technician.completed', [
            'summary' => [
                'total' => $total,
                'avg_time' => $avgTime,
                'high_urgency' => $highUrgency,
            ],
            'rows' => $rows,
            'pagination' => $paginated,
        ]);
    }

    public function profile()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Technician') {
            abort(403);
        }

        return view('Technician.profile', ['user' => $user]);
    }

    public function updateProfile(Request $request)
    {
        $authUser = Auth::user();
        if (!$authUser || $authUser->role !== 'Technician') {
            abort(403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $authUser->id],
            // Digits only; we will prepend +60 before saving
            // Allow 9-11 digits; a single leading 0 will be trimmed before storing with +60
            'phone_number_digits' => ['nullable', 'regex:/^[0-9]{9,11}$/'],
            'campus' => ['nullable', Rule::in(['Penang'])],
            'specialization' => ['nullable', 'array'],
            'specialization.*' => [Rule::in([
                'Electrical',
                'Networking',
                'AirConditioning',
                'Plumbing',
                'Carpentry',
                'AudioVisual',
                'Landscaping',
                'Security',
                'Cleaning',
            ])],
            'availability_status' => ['nullable', 'in:Available,Busy,On_Leave'],
        ]);

        // Normalize phone: allow optional leading 0, then store as +60########
        $phoneDigits = $data['phone_number_digits'] ?? null;
        if ($phoneDigits !== null && $phoneDigits !== '') {
            if (str_starts_with($phoneDigits, '0')) {
                $phoneDigits = substr($phoneDigits, 1);
            }

            // Enforce final length after trimming leading zero
            if (strlen($phoneDigits) < 8 || strlen($phoneDigits) > 10) {
                return back()
                    ->withErrors(['phone_number_digits' => 'Phone number must be 8-10 digits after the +60 prefix.'])
                    ->withInput();
            }

            $data['phone_number'] = '+60' . $phoneDigits;
        } else {
            $data['phone_number'] = null;
        }
        unset($data['phone_number_digits']);

        // Store multiple selections as a comma-separated string
        $specializationValues = $data['specialization'] ?? [];
        $data['specialization'] = $specializationValues ? implode(',', $specializationValues) : null;

        // Load the concrete Eloquent User model instance so fill() is available to static analysis
        /** @var \App\Models\User $userModel */
        $userModel = User::findOrFail($authUser->id);
        $userModel->fill($data);
        $userModel->save();

        return back()->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Technician') {
            abort(403);
        }

        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        /** @var \App\Models\User $userModel */
        $userModel = User::findOrFail($user->id);
        $userModel->password = Hash::make($request->new_password);
        $userModel->save();

        return back()->with('success', 'Password updated.');
    }

    public function taskDetail($id)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Technician')
            abort(403);

        $job = Report::with([
            'room.block.campus',
            'category',
            'attachments', // 用于 before/after photos
        ])
            ->where('technician_id', $user->id)
            ->findOrFail($id);

        $afterPhotos = $job->attachments
            ->where('attachment_type', 'TECHNICIAN_PROOF')
            ->values();

        return view('Technician.task-detail', [
            'job' => $job,
            'afterPhotos' => $afterPhotos,
        ]);
    }

    public function updateStatus(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Technician')
            abort(403);

        $request->validate([
            'status' => ['required', 'in:In_Progress'],
        ]);

        $job = Report::where('technician_id', $user->id)->findOrFail($id);
        if ($job->status === 'Completed') {
            return back()->withErrors(['status' => 'Task is already completed and cannot be changed.']);
        }

        $job->status = $request->status;
        $job->save();

        return back()->with('success', 'Status updated.');
    }

    public function completeJob(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Technician')
            abort(403);

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

        $before = optional($job->attachments->firstWhere('attachment_type', 'REPORTER_PROOF'))->file_path;
        $after = optional($job->attachments->firstWhere('attachment_type', 'TECHNICIAN_PROOF'))->file_path;

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
                'location' => [
                    'campus' => optional(optional(optional($job->room)->block)->campus)->campus_name,
                    'block' => optional(optional($job->room)->block)->block_name,
                    'room' => optional($job->room)->room_name,
                ],
                'category' => optional($job->category)->name,
                'attachments' => [
                    'reporter_proof' => $before ? asset('storage/' . $before) : null,
                    'technician_proof' => $after ? asset('storage/' . $after) : null,
                ],
            ],
        ], 200);
    }

}

<?php
 
// Author: Ivan Goh Shern Rune

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Serve the all technicians blade view.
     */
    public function allTechnicians()
    {
        return view('Technician.all-technician');
    }

    /**
     * Serve the add technician blade view.
     */
    /**
     * Serve the add technician blade view.
     */
    public function addTechnician()
    {
        // Change from 'admin.add-technician' to 'admin.technicians.add-technician'
        return view('admin.technicians.add_technician');
    }


    /**
     * Serve the edit technician blade view.
     */
    public function editTechnician($id)
    {
        return view('Technician.edit-technician');
    }

    /**
     * API endpoint to create a new technician account.
     */
    public function createTechnicianApi(Request $request)
    {
        $authUser = Auth::user();

        // Authentication check
        if (!$authUser) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Authorization check - only Admin can create technicians
        if ($authUser->role !== 'Admin') {
            return response()->json([
                'status' => 403,
                'message' => 'Forbidden - Admin access only'
            ], 403);
        }

        // Validate the request data
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:100'],
                'email' => ['required', 'email', 'max:100', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        try {
            // Create the technician user
            $technician = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'Technician',
                'phone_number' => null,
                'campus' => null,
                'specialization' => null,
                'availability_status' => 'Available',
                'reporter_role' => null,
                'admin_level' => null,
            ]);

            return response()->json([
                'status' => 201,
                'message' => 'Technician account created successfully',
                'data' => [
                    'id' => $technician->id,
                    'name' => $technician->name,
                    'email' => $technician->email,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to create technician: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint to list all technicians.
     */
    public function listTechniciansApi(Request $request)
    {
        $authUser = Auth::user();

        if (!$authUser || $authUser->role !== 'Technician' && $authUser->role !== 'Admin') {
            return response()->json([
                'status' => 403,
                'message' => 'Forbidden'
            ], 403);
        }

        $query = User::where('role', 'Technician');

        // Optional search filter
        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Optional filters
        if ($request->filled('campus')) {
            $query->where('campus', $request->campus);
        }

        if ($request->filled('availability_status')) {
            $query->where('availability_status', $request->availability_status);
        }

        $technicians = $query->orderBy('name')->paginate(15);

        // Use getCollection()->transform() instead of map() to preserve pagination
        $technicians->getCollection()->transform(function (User $tech) {
            return [
                'id' => $tech->id,
                'name' => $tech->name,
                'email' => $tech->email,
                'phone_number' => $tech->phone_number ?? '-',
                'campus' => $tech->campus ?? '-',
                'specialization' => $tech->specialization ?? '-',
                'availability_status' => $tech->availability_status ?? '-',
                'created_at' => optional($tech->created_at)?->format('d M Y') ?? '-',
            ];
        });

        return response()->json([
            'status' => 200,
            'data' => [
                'technicians' => $technicians->items(), // Get the items array
                'pagination' => [
                    'current_page' => $technicians->currentPage(),
                    'last_page' => $technicians->lastPage(),
                    'total' => $technicians->total(),
                ],
            ],
        ]);
    }

    /**
     * API endpoint to get a single technician.
     */
    public function getTechnicianApi(int $id)
    {
        $authUser = Auth::user();

        if (!$authUser || $authUser->role !== 'Admin') {
            return response()->json([
                'status' => 403,
                'message' => 'Forbidden'
            ], 403);
        }

        $technician = User::where('role', 'Technician')->findOrFail($id);

        // Convert specialization string to array
        $specializationArray = $technician->specialization
            ? explode(',', $technician->specialization)
            : [];

        // Extract phone digits (remove +60 prefix)
        $phoneDigits = $technician->phone_number
            ? str_replace('+60', '', $technician->phone_number)
            : '';

        return response()->json([
            'status' => 200,
            'data' => [
                'id' => $technician->id,
                'name' => $technician->name,
                'email' => $technician->email,
                'phone_number' => $technician->phone_number,
                'phone_number_digits' => $phoneDigits,
                'campus' => $technician->campus,
                'specialization' => $specializationArray,
                'availability_status' => $technician->availability_status,
                'created_at' => optional($technician->created_at)?->format('d M Y'),
            ],
        ]);
    }

    /**
     * API endpoint to update a technician account.
     */
    public function updateTechnicianApi(Request $request, int $id)
    {
        $authUser = Auth::user();

        if (!$authUser || $authUser->role !== 'Admin') {
            return response()->json([
                'status' => 403,
                'message' => 'Forbidden'
            ], 403);
        }

        $technician = User::where('role', 'Technician')->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email,' . $id],
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

        // Process phone number
        $phoneDigits = $validated['phone_number_digits'] ?? null;
        if ($phoneDigits !== null && $phoneDigits !== '') {
            if (str_starts_with($phoneDigits, '0')) {
                $phoneDigits = substr($phoneDigits, 1);
            }

            if (strlen($phoneDigits) < 8 || strlen($phoneDigits) > 10) {
                return response()->json([
                    'status' => 422,
                    'errors' => [
                        'phone_number_digits' => ['Phone number must be 8-10 digits after the +60 prefix.']
                    ],
                ], 422);
            }

            $phoneNumber = '+60' . $phoneDigits;
        } else {
            $phoneNumber = null;
        }

        // Process specialization
        $specializationValues = $validated['specialization'] ?? [];
        $specialization = $specializationValues ? implode(',', $specializationValues) : null;

        // Update technician
        $technician->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $phoneNumber,
            'campus' => $validated['campus'] ?? null,
            'specialization' => $specialization,
            'availability_status' => $validated['availability_status'] ?? 'Available',
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Technician account updated successfully',
        ]);
    }

    /**
     * API endpoint to delete a technician account.
     */
    public function deleteTechnicianApi(int $id)
    {
        $authUser = Auth::user();

        if (!$authUser || $authUser->role !== 'Admin') {
            return response()->json([
                'status' => 403,
                'message' => 'Forbidden'
            ], 403);
        }

        $technician = User::where('role', 'Technician')->findOrFail($id);

        // Check if technician has assigned reports
        $hasActiveReports = \DB::table('reports')
            ->where('technician_id', $id)
            ->whereIn('status', ['Assigned', 'In_Progress'])
            ->exists();

        if ($hasActiveReports) {
            return response()->json([
                'status' => 422,
                'message' => 'Cannot delete technician with active assigned reports',
            ], 422);
        }

        $technician->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Technician account deleted successfully',
        ]);
    }
}

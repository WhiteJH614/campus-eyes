<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class TechnicianController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'technician');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $technicians = $query->get();

        return view('admin.technicians.index', compact('technicians'));
    }

    public function destroy($id)
{
    $technician = User::where('id', $id)
        ->where('role', 'technician')
        ->firstOrFail();

    $technician->delete();

    return redirect()
        ->route('admin.technicians.index')
        ->with('success', 'Technician removed');
}
}


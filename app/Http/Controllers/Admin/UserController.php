<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'Reporter')
            ->where('reporter_role', 'Student')
            ->get();

        return view('admin.users.index', compact('users'));
    }
    
    public function destroy($id)
{
    $user = User::where('id', $id)
        ->where('role', 'Student')
        ->firstOrFail();

    $user->delete();

    return back()->with('success', 'Student deleted');
}
}

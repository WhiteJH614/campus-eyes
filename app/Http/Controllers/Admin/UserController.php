<?php


// Author: Ivan Goh Shern Rune

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
        $user = User::findOrFail($id);

        // ✅ 验证是否是 Reporter
        if ($user->role !== 'Reporter') {
            return back()->with('error', 'Invalid user type.');
        }

        // ✅ 检查关联的报告（如果 User 模型有 reports() 关系）
        if ($user->reports()->exists()) {
            $reportCount = $user->reports()->count();
            return back()->with('error', "Cannot delete this student. They have {$reportCount} report(s).");
        }

        $user->delete();

        return back()->with('success', 'Student deleted successfully');
    }
}

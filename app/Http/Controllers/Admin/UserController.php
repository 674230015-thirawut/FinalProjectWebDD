<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount(['tickets', 'assignedTickets'])
            ->orderBy('role')
            ->orderBy('id')
            ->get();
        $roles = UserRole::cases();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|string|in:user,technician,admin',
        ]);

        $user->update([
            'role' => UserRole::from($validated['role']),
        ]);

        return back()->with('success', "เปลี่ยนบทบาทของ {$user->name} เป็น {$user->role->label()} สำเร็จ");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'ไม่สามารถลบบัญชีของตัวเองได้');
        }

        $user->delete();

        return back()->with('success', 'ลบผู้ใช้งานเรียบร้อย');
    }
}

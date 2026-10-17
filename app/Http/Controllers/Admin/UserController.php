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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:user,technician,admin',
            'department' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => UserRole::from($validated['role']),
            'department' => $validated['department'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ]);

        return back()->with('success', 'เพิ่มผู้ใช้งานใหม่สำเร็จ');
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

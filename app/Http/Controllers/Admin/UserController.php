<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Show users management page
     */
    public function users(Request $request)
    {
        $query = User::query()->with(['defaultAddress', 'addresses']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active' ? 1 : 0);
        }

        // Sort
        $sortBy    = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate(15);

        return view('admin.users', compact('users'));
    }

    /**
     * Update user role
     */
    public function updateUserRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:admin,user']);
        $user->update(['role' => $request->role]);
        return back()->with('success', "Đã đổi vai trò của \"{$user->name}\" thành " . ($request->role === 'admin' ? 'Admin' : 'User') . '!');
    }

    /**
     * Toggle user active status (vô hiệu hóa / mở lại)
     */
    public function toggleUserStatus(User $user)
    {
        // Không thể vô hiệu hóa chính mình
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể vô hiệu hóa tài khoản của chính bạn!');
        }

        $newStatus = !$user->is_active;
        $user->update(['is_active' => $newStatus]);

        $msg = $newStatus
            ? "Đã mở lại tài khoản \"{$user->name}\"!"
            : "Đã vô hiệu hóa tài khoản \"{$user->name}\"!";

        return back()->with('success', $msg);
    }

    /**
     * Create new user
     */
    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
            ],
            'role' => 'required|in:admin,user',
            'phone' => 'nullable|string|max:20',
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email phải đúng định dạng',
            'email.unique' => 'Email này đã được sử dụng',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự và có chữ hoa, chữ thường',
            'password.regex' => 'Mật khẩu phải có ít nhất 8 ký tự và có chữ hoa, chữ thường',
            'role.required' => 'Vui lòng chọn vai trò',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
        ]);

        return back()->with('success', 'Tạo người dùng mới thành công!');
    }
}

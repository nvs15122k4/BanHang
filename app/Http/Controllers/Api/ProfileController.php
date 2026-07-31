<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json(Auth::user()->load('addresses'));
    }

    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $url = (new CloudinaryService)->uploadImage($request->file('avatar'));

        $user = Auth::user();
        $user->update(['avatar' => $url]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật ảnh đại diện thành công!',
            'data' => $user->fresh(),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required_without:avatar|string|max:255',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'birthday' => 'nullable|date',
            'height' => 'nullable|integer|min:100|max:300',
            'weight' => 'nullable|numeric|min:20|max:300',
            'avatar' => ['nullable', 'string', 'url', 'max:2048'],
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin thành công!',
            'data' => $user->fresh()->load('addresses'),
        ]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed',
        ]);

        Auth::user()->update(['password' => Hash::make($request->password)]);

        return response()->json(['success' => true, 'message' => 'Đổi mật khẩu thành công!']);
    }

    // ─── Addresses ─────────────────────────────────────────────────────────

    public function addresses(): JsonResponse
    {
        $addresses = Auth::user()->addresses()->orderBy('is_default', 'desc')->get();
        return response()->json(['data' => $addresses]);
    }

    public function storeAddress(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'province' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'ward' => 'nullable|string|max:255',
            'detail' => 'required|string|max:500',
            'is_default' => 'boolean',
        ]);

        $address = Auth::user()->addresses()->create($validated);

        if ($request->boolean('is_default')) {
            Auth::user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        return response()->json(['success' => true, 'data' => $address], 201);
    }

    public function updateAddress(Request $request, Address $address): JsonResponse
    {
        if ($address->user_id !== Auth::id()) {
            return response()->json(['message' => 'Không có quyền'], 403);
        }

        $validated = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'province' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'ward' => 'nullable|string|max:255',
            'detail' => 'required|string|max:500',
            'is_default' => 'boolean',
        ]);

        $address->update($validated);

        if ($request->boolean('is_default')) {
            Auth::user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        return response()->json(['success' => true, 'data' => $address->fresh()]);
    }

    public function destroyAddress(Address $address): JsonResponse
    {
        if ($address->user_id !== Auth::id()) {
            return response()->json(['message' => 'Không có quyền'], 403);
        }

        $address->delete();

        return response()->json(['success' => true, 'message' => 'Đã xóa địa chỉ!']);
    }

    public function setDefaultAddress(Address $address): JsonResponse
    {
        if ($address->user_id !== Auth::id()) {
            return response()->json(['message' => 'Không có quyền'], 403);
        }

        Auth::user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return response()->json(['success' => true]);
    }
}

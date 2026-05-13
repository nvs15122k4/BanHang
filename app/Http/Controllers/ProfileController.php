<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show profile page
     */
    public function index()
    {
        $user      = Auth::user();
        $addresses = $user->addresses()->orderBy('is_default', 'desc')->get();

        return view('profile.index', compact('user', 'addresses'));
    }

    /**
     * Update profile information (supports AJAX)
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone'  => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
        ], [
            'name.required'  => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email'    => 'Email phải đúng định dạng',
            'email.unique'   => 'Email này đã được sử dụng',
        ]);

        $user->update($validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Cập nhật thông tin thành công!']);
        }

        return back()->with('success', 'Cập nhật thông tin thành công!');
    }

    /**
     * Update password (supports AJAX)
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/'],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại',
            'password.required'         => 'Vui lòng nhập mật khẩu mới',
            'password.confirmed'        => 'Xác nhận mật khẩu không khớp',
            'password.min'              => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password.regex'            => 'Mật khẩu phải có chữ hoa và chữ thường',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => ['current_password' => ['Mật khẩu hiện tại không đúng']]], 422);
            }
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Đổi mật khẩu thành công!']);
        }

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }

    /**
     * Store new address (supports AJAX)
     */
    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone'          => 'required|string|max:20',
            'province'       => 'required|string|max:255',
            'district'       => 'required|string|max:255',
            'ward'           => 'required|string|max:255',
            'detail'         => 'required|string',
            'is_default'     => 'boolean',
        ], [
            'recipient_name.required' => 'Vui lòng nhập tên người nhận',
            'phone.required'          => 'Vui lòng nhập số điện thoại',
            'province.required'       => 'Vui lòng nhập tỉnh/thành phố',
            'district.required'       => 'Vui lòng nhập quận/huyện',
            'ward.required'           => 'Vui lòng nhập phường/xã',
            'detail.required'         => 'Vui lòng nhập địa chỉ chi tiết',
        ]);

        $user = Auth::user();

        if ($request->boolean('is_default')) {
            $user->addresses()->update(['is_default' => false]);
        }

        if ($user->addresses()->count() === 0) {
            $validated['is_default'] = true;
        }

        $address = $user->addresses()->create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thêm địa chỉ thành công!',
                'address' => $address,
            ]);
        }

        return back()->with('success', 'Thêm địa chỉ thành công!');
    }

    /**
     * Update address (supports AJAX)
     */
    public function updateAddress(Request $request, Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone'          => 'required|string|max:20',
            'province'       => 'required|string|max:255',
            'district'       => 'required|string|max:255',
            'ward'           => 'required|string|max:255',
            'detail'         => 'required|string',
            'is_default'     => 'boolean',
        ], [
            'recipient_name.required' => 'Vui lòng nhập tên người nhận',
            'phone.required'          => 'Vui lòng nhập số điện thoại',
            'province.required'       => 'Vui lòng nhập tỉnh/thành phố',
            'district.required'       => 'Vui lòng nhập quận/huyện',
            'ward.required'           => 'Vui lòng nhập phường/xã',
            'detail.required'         => 'Vui lòng nhập địa chỉ chi tiết',
        ]);

        if ($request->boolean('is_default')) {
            Auth::user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật địa chỉ thành công!',
                'address' => $address->fresh(),
            ]);
        }

        return back()->with('success', 'Cập nhật địa chỉ thành công!');
    }

    /**
     * Delete address (supports AJAX)
     */
    public function destroyAddress(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $first = Auth::user()->addresses()->first();
            if ($first) {
                $first->update(['is_default' => true]);
            }
        }

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Xóa địa chỉ thành công!']);
        }

        return back()->with('success', 'Xóa địa chỉ thành công!');
    }

    /**
     * Set address as default (supports AJAX)
     */
    public function setDefaultAddress(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        Auth::user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Đã đặt làm địa chỉ mặc định!']);
        }

        return back()->with('success', 'Đã đặt làm địa chỉ mặc định!');
    }
}

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
        $user = Auth::user();
        $addresses = $user->addresses()->orderBy('is_default', 'desc')->get();
        
        return view('profile.index', compact('user', 'addresses'));
    }

    /**
     * Update profile information
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'birthday' => 'nullable|date',
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email phải đúng định dạng',
            'email.unique' => 'Email này đã được sử dụng',
        ]);

        $user->update($validated);

        return redirect()->route('profile.index')->with('success', 'Cập nhật thông tin thành công!');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
            ],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại',
            'password.required' => 'Vui lòng nhập mật khẩu mới',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự và có chữ hoa, chữ thường',
            'password.regex' => 'Mật khẩu phải có ít nhất 8 ký tự và có chữ hoa, chữ thường',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }

    /**
     * Store new address
     */
    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'province' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'detail' => 'required|string',
            'is_default' => 'boolean',
        ], [
            'recipient_name.required' => 'Vui lòng nhập tên người nhận',
            'phone.required' => 'Vui lòng nhập số điện thoại',
            'province.required' => 'Vui lòng chọn Tỉnh/Thành phố',
            'district.required' => 'Vui lòng chọn Quận/Huyện',
            'ward.required' => 'Vui lòng chọn Phường/Xã',
            'detail.required' => 'Vui lòng nhập địa chỉ chi tiết',
        ]);

        $user = Auth::user();

        // If this is set as default, unset other defaults
        if ($request->boolean('is_default')) {
            $user->addresses()->update(['is_default' => false]);
        }

        // If this is the first address, make it default
        if ($user->addresses()->count() === 0) {
            $validated['is_default'] = true;
        }

        $user->addresses()->create($validated);

        return redirect()->route('profile.index')->with('success', 'Thêm địa chỉ thành công!');
    }

    /**
     * Update address
     */
    public function updateAddress(Request $request, Address $address)
    {
        // Check if address belongs to current user
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'province' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'detail' => 'required|string',
            'is_default' => 'boolean',
        ], [
            'recipient_name.required' => 'Vui lòng nhập tên người nhận',
            'phone.required' => 'Vui lòng nhập số điện thoại',
            'province.required' => 'Vui lòng chọn Tỉnh/Thành phố',
            'district.required' => 'Vui lòng chọn Quận/Huyện',
            'ward.required' => 'Vui lòng chọn Phường/Xã',
            'detail.required' => 'Vui lòng nhập địa chỉ chi tiết',
        ]);

        // If this is set as default, unset other defaults
        if ($request->boolean('is_default')) {
            Auth::user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($validated);

        return redirect()->route('profile.index')->with('success', 'Cập nhật địa chỉ thành công!');
    }

    /**
     * Delete address
     */
    public function destroyAddress(Address $address)
    {
        // Check if address belongs to current user
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $wasDefault = $address->is_default;
        $address->delete();

        // If deleted address was default, set another as default
        if ($wasDefault) {
            $firstAddress = Auth::user()->addresses()->first();
            if ($firstAddress) {
                $firstAddress->update(['is_default' => true]);
            }
        }

        return redirect()->route('profile.index')->with('success', 'Xóa địa chỉ thành công!');
    }

    /**
     * Set address as default
     */
    public function setDefaultAddress(Address $address)
    {
        // Check if address belongs to current user
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        // Unset all defaults
        Auth::user()->addresses()->update(['is_default' => false]);

        // Set this as default
        $address->update(['is_default' => true]);

        return back()->with('success', 'Đã đặt làm địa chỉ mặc định!');
    }
}

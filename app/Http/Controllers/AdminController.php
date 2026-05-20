<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        // Statistics
        $stats = [
            'total_users' => User::count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_customers' => User::where('role', 'user')->count(),
            'total_products' => Product::count(),
            'active_products' => Product::where('trang_thai', 'con')->count(),
            'inactive_products' => Product::where('trang_thai', 'het')->count(),
            'total_stock' => Product::sum('so_luong'),
            'low_stock_products' => Product::where('so_luong', '<', 10)->count(),
        ];

        // Recent users (last 5)
        $recentUsers = User::orderBy('created_at', 'desc')->take(5)->get();

        // Recent products (last 5)
        $recentProducts = Product::orderBy('created_at', 'desc')->take(5)->get();

        // Low stock products
        $lowStockProducts = Product::where('so_luong', '<', 10)
            ->orderBy('so_luong', 'asc')
            ->take(10)
            ->get();

        // Products by status
        $productsByStatus = Product::select('trang_thai', DB::raw('count(*) as total'))
            ->groupBy('trang_thai')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentUsers',
            'recentProducts',
            'lowStockProducts',
            'productsByStatus'
        ));
    }

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
     * Show products management page
     */
    public function products(Request $request)
    {
        $query = Product::query();

        if ($request->get('trash') === 'only') {
            $query->onlyTrashed();
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ten_sp', 'like', "%{$search}%")
                  ->orWhere('mo_ta', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('trang_thai', $request->status); // con / het
        }

        // Filter by loai
        if ($request->filled('loai')) {
            $query->where('loai', $request->loai);
        }

        // Filter by stock
        if ($request->filled('stock_filter')) {
            switch ($request->stock_filter) {
                case 'low':
                    $query->where('so_luong', '<', 10);
                    break;
                case 'out':
                    $query->where('so_luong', 0);
                    break;
                case 'in':
                    $query->where('so_luong', '>', 0);
                    break;
            }
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    /**
     * Quick update product status
     */
    public function updateProductStatus(Request $request, Product $product)
    {
        $request->validate([
            'trang_thai' => 'required|in:con,het',
        ]);

        $product->update(['trang_thai' => $request->trang_thai]);

        if ($request->expectsJson()) {
            return response()->json([
                'success'    => true,
                'trang_thai' => $product->trang_thai,
                'message'    => 'Cập nhật trạng thái thành công!',
            ]);
        }

        return back()->with('success', 'Cập nhật trạng thái sản phẩm thành công!');
    }

    /**
     * Quick update product stock
     */
    public function updateProductStock(Request $request, Product $product)
    {
        $request->validate([
            'so_luong' => 'required|integer|min:0',
        ]);

        $product->update(['so_luong' => $request->so_luong]);

        if ($request->expectsJson()) {
            return response()->json([
                'success'  => true,
                'so_luong' => $product->so_luong,
                'message'  => 'Cập nhật số lượng thành công!',
            ]);
        }

        return back()->with('success', 'Cập nhật số lượng thành công!');
    }

    /**
     * Restore a soft-deleted product.
     */
    public function restoreProduct(int $productId)
    {
        $product = Product::onlyTrashed()->findOrFail($productId);
        $product->restore();

        return back()->with('success', "Đã khôi phục sản phẩm \"{$product->ten_sp}\".");
    }

    /**
     * Show statistics page
     */
    public function statistics()
    {
        // User statistics
        $userStats = [
            'total' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'users' => User::where('role', 'user')->count(),
            'with_addresses' => User::has('addresses')->count(),
            'without_addresses' => User::doesntHave('addresses')->count(),
        ];

        // Product statistics
        $productStats = [
            'total' => Product::count(),
            'active' => Product::where('trang_thai', 'con')->count(),
            'inactive' => Product::where('trang_thai', 'het')->count(),
            'total_stock' => Product::sum('so_luong'),
            'avg_price' => Product::avg('gia'),
            'max_price' => Product::max('gia'),
            'min_price' => Product::min('gia'),
            'low_stock' => Product::where('so_luong', '<', 10)->count(),
            'out_of_stock' => Product::where('so_luong', 0)->count(),
        ];

        // Order statistics
        $orderStats = [
            'total' => \App\Models\Order::count(),
            'pending' => \App\Models\Order::where('trang_thai', 'pending')->count(),
            'completed' => \App\Models\Order::where('trang_thai', 'completed')->count(),
            'cancelled' => \App\Models\Order::where('trang_thai', 'cancelled')->count(),
            'revenue' => \App\Models\Order::where('trang_thai', 'completed')->sum('thanh_tien'),
        ];

        // Top 10 most expensive products
        $topExpensiveProducts = Product::orderBy('gia', 'desc')->take(10)->get();

        // Top 10 cheapest products
        $topCheapestProducts = Product::orderBy('gia', 'asc')->take(10)->get();

        // Top 10 highest stock products
        $topStockProducts = Product::orderBy('so_luong', 'desc')->take(10)->get();

        // Helper to fill last 12 months
        $last12Months = [];
        for ($i = 11; $i >= 0; $i--) {
            $last12Months[] = now()->subMonths($i)->format('Y-m');
        }

        $usersPerMonthData = collect($last12Months)->mapWithKeys(fn($m) => [$m => 0]);
        $productsPerMonthData = collect($last12Months)->mapWithKeys(fn($m) => [$m => 0]);
        $revenuePerMonthData = collect($last12Months)->mapWithKeys(fn($m) => [$m => 0]);

        User::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->get()
            ->each(fn($item) => $usersPerMonthData[$item->month] = $item->total);

        Product::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->get()
            ->each(fn($item) => $productsPerMonthData[$item->month] = $item->total);

        \App\Models\Order::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('SUM(thanh_tien) as total'))
            ->where('trang_thai', 'completed')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->get()
            ->each(fn($item) => $revenuePerMonthData[$item->month] = (float)$item->total);

        $usersPerMonth = collect($usersPerMonthData)->map(fn($total, $month) => ['month' => $month, 'total' => $total])->values();
        $productsPerMonth = collect($productsPerMonthData)->map(fn($total, $month) => ['month' => $month, 'total' => $total])->values();
        $revenuePerMonth = collect($revenuePerMonthData)->map(fn($total, $month) => ['month' => $month, 'total' => $total])->values();

        // Category distribution
        $categoryDistribution = Product::select('loai', DB::raw('count(*) as total'))
            ->groupBy('loai')
            ->get();

        // Order status distribution
        $orderStatusDistribution = \App\Models\Order::select('trang_thai', DB::raw('count(*) as total'))
            ->groupBy('trang_thai')
            ->get();

        return view('admin.statistics', compact(
            'userStats',
            'productStats',
            'orderStats',
            'topExpensiveProducts',
            'topCheapestProducts',
            'topStockProducts',
            'usersPerMonth',
            'productsPerMonth',
            'revenuePerMonth',
            'categoryDistribution',
            'orderStatusDistribution'
        ));
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

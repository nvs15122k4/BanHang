<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
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
}

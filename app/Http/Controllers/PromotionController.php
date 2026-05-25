<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\PromotionItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    /**
     * Admin: danh sách khuyến mãi
     */
    public function index(Request $request)
    {
        $query = Promotion::with('items')->latest();

        if ($request->filled('search')) {
            $query->where('ten', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('trang_thai')) {
            if ($request->trang_thai === 'running') {
                $query->where('trang_thai', 'active')
                      ->where('ngay_bat_dau', '<=', now())
                      ->where('ngay_ket_thuc', '>=', now());
            } else {
                $query->where('trang_thai', $request->trang_thai);
            }
        }
        if ($request->filled('pham_vi')) {
            $query->where('pham_vi', $request->pham_vi);
        }

        $promotions = $query->paginate(15)->withQueryString();

        return view('admin.promotions.index', compact('promotions'));
    }

    /**
     * Admin: form tạo khuyến mãi
     */
    public function create()
    {
        $categories = Product::whereNotNull('loai')->where('loai', '!=', '')
                              ->distinct()->orderBy('loai')->pluck('loai');
        $products   = Product::where('trang_thai', 'con')->orderBy('ten_sp')->limit(500)->get(['id', 'ten_sp', 'loai', 'gia']);
        
        $promotion = new Promotion(); // Trống cho create form
        return view('admin.promotions.create', compact('categories', 'products', 'promotion'));
    }

    /**
     * Admin: tạo khuyến mãi mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten'           => 'required|string|max:255',
            'mo_ta'         => 'nullable|string|max:1000',
            'loai_km'       => 'required|in:percent,fixed',
            'gia_tri'       => 'required|numeric|min:0',
            'gia_tri_toi_da'=> 'nullable|numeric|min:0',
            'ngay_bat_dau'  => 'required|date',
            'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
            'pham_vi'       => 'required|in:all,category,product',
            'trang_thai'    => 'required|in:active,inactive,scheduled',
            'tag'           => 'nullable|string|max:50',
            // Usage limits
            'usage_limit'   => 'nullable|integer|min:0',
            'usage_limit_per_user' => 'nullable|integer|min:0',
            // Phạm vi items
            'categories'    => 'nullable|array',
            'product_ids'   => 'nullable|array',
        ], [
            'ten.required'           => 'Vui lòng nhập tên khuyến mãi',
            'gia_tri.required'       => 'Vui lòng nhập giá trị giảm',
            'ngay_bat_dau.required'  => 'Vui lòng chọn ngày bắt đầu',
            'ngay_ket_thuc.required' => 'Vui lòng chọn ngày kết thúc',
            'ngay_ket_thuc.after'    => 'Ngày kết thúc phải sau ngày bắt đầu',
            'pham_vi.required'       => 'Vui lòng chọn phạm vi áp dụng',
            'usage_limit.min'        => 'Giới hạn sử dụng phải là số nguyên không âm',
            'usage_limit_per_user.min' => 'Giới hạn sử dụng mỗi người phải là số nguyên không âm',
        ]);

        // Validate percent ≤ 100
        if ($validated['loai_km'] === 'percent' && $validated['gia_tri'] > 100) {
            return back()->withErrors(['gia_tri' => 'Phần trăm giảm không được vượt quá 100%'])->withInput();
        }

        DB::beginTransaction();
        try {
            $promotion = Promotion::create([
                'ten'            => $validated['ten'],
                'mo_ta'          => $validated['mo_ta'] ?? null,
                'loai_km'        => $validated['loai_km'],
                'gia_tri'        => $validated['gia_tri'],
                'gia_tri_toi_da' => $validated['gia_tri_toi_da'] ?? null,
                'ngay_bat_dau'   => $validated['ngay_bat_dau'],
                'ngay_ket_thuc'  => $validated['ngay_ket_thuc'],
                'pham_vi'        => $validated['pham_vi'],
                'trang_thai'     => $validated['trang_thai'],
                'tag'            => $validated['tag'] ?? null,
                'used_count'     => 0,
                'usage_limit'    => $validated['usage_limit'] ?? null,
                'usage_limit_per_user' => $validated['usage_limit_per_user'] ?? null,
            ]);

            // Lưu phạm vi items
            if ($validated['pham_vi'] === 'category' && !empty($validated['categories'])) {
                foreach ($validated['categories'] as $cat) {
                    PromotionItem::create([
                        'promotion_id' => $promotion->id,
                        'loai'         => 'category',
                        'gia_tri'      => $cat,
                    ]);
                }
            } elseif ($validated['pham_vi'] === 'product' && !empty($validated['product_ids'])) {
                foreach ($validated['product_ids'] as $pid) {
                    PromotionItem::create([
                        'promotion_id' => $promotion->id,
                        'loai'         => 'product',
                        'gia_tri'      => (string) $pid,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.promotions.index')->with('success', 'Tạo khuyến mãi thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Admin: form sửa khuyến mãi
     */
    public function edit(Promotion $promotion)
    {
        $categories = Product::whereNotNull('loai')->where('loai', '!=', '')
                              ->distinct()->orderBy('loai')->pluck('loai');
        $products   = Product::where('trang_thai', 'con')->orderBy('ten_sp')->limit(500)->get(['id', 'ten_sp', 'loai', 'gia']);
        
        // Load items if it's not loaded
        $promotion->load('items');

        return view('admin.promotions.edit', compact('categories', 'products', 'promotion'));
    }

    /**
     * Admin: cập nhật khuyến mãi
     */
    public function update(Request $request, Promotion $promotion)
    {
        $validated = $request->validate([
            'ten'           => 'required|string|max:255',
            'mo_ta'         => 'nullable|string|max:1000',
            'loai_km'       => 'required|in:percent,fixed',
            'gia_tri'       => 'required|numeric|min:0',
            'gia_tri_toi_da'=> 'nullable|numeric|min:0',
            'ngay_bat_dau'  => 'required|date',
            'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
            'pham_vi'       => 'required|in:all,category,product',
            'trang_thai'    => 'required|in:active,inactive,scheduled',
            'tag'           => 'nullable|string|max:50',
            // Usage limits
            'usage_limit'   => 'nullable|integer|min:0',
            'usage_limit_per_user' => 'nullable|integer|min:0',
            'categories'    => 'nullable|array',
            'product_ids'   => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $promotion->update([
                'ten'            => $validated['ten'],
                'mo_ta'          => $validated['mo_ta'] ?? null,
                'loai_km'        => $validated['loai_km'],
                'gia_tri'        => $validated['gia_tri'],
                'gia_tri_toi_da' => $validated['gia_tri_toi_da'] ?? null,
                'ngay_bat_dau'   => $validated['ngay_bat_dau'],
                'ngay_ket_thuc'  => $validated['ngay_ket_thuc'],
                'pham_vi'        => $validated['pham_vi'],
                'trang_thai'     => $validated['trang_thai'],
                'tag'            => $validated['tag'] ?? null,
                'usage_limit'    => $validated['usage_limit'] ?? null,
                'usage_limit_per_user' => $validated['usage_limit_per_user'] ?? null,
            ]);

            // Xóa items cũ và tạo lại
            $promotion->items()->delete();

            if ($validated['pham_vi'] === 'category' && !empty($validated['categories'])) {
                foreach ($validated['categories'] as $cat) {
                    PromotionItem::create([
                        'promotion_id' => $promotion->id,
                        'loai'         => 'category',
                        'gia_tri'      => $cat,
                    ]);
                }
            } elseif ($validated['pham_vi'] === 'product' && !empty($validated['product_ids'])) {
                foreach ($validated['product_ids'] as $pid) {
                    PromotionItem::create([
                        'promotion_id' => $promotion->id,
                        'loai'         => 'product',
                        'gia_tri'      => (string) $pid,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.promotions.index')->with('success', 'Cập nhật khuyến mãi thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Admin: xóa khuyến mãi
     */
    public function destroy(Promotion $promotion)
    {
        $name = $promotion->ten;
        $promotion->delete(); // cascade xóa items
        return redirect()->route('admin.promotions.index')
            ->with('success', "Đã xóa khuyến mãi \"{$name}\"!");
    }

    /**
     * Admin: bật/tắt khuyến mãi
     */
    public function toggle(Promotion $promotion)
    {
        $newStatus = $promotion->trang_thai === 'active' ? 'inactive' : 'active';
        $promotion->update(['trang_thai' => $newStatus]);

        $msg = $newStatus === 'active'
            ? "Đã bật khuyến mãi \"{$promotion->ten}\"!"
            : "Đã tắt khuyến mãi \"{$promotion->ten}\"!";

        if (request()->ajax()) {
            return response()->json(['success' => true, 'trang_thai' => $newStatus, 'message' => $msg]);
        }
        return back()->with('success', $msg);
    }

    /**
     * Public: trang khuyến mãi cho khách hàng
     */
    public function publicIndex(Request $request)
    {
        // Lấy tất cả KM đang active
        $activePromotions = Promotion::currentlyActive()->with('items')->get();

        $categoryFilter = (string) $request->input('loai_filter', $request->input('loai', ''));
        $search = trim((string) $request->input('search', ''));

        // Lấy danh sách sản phẩm (giới hạn 1000 để tránh tràn bộ nhớ nếu database quá lớn)
        $productsQuery = Product::where('trang_thai', 'con')->with(['wishlists']);

        if ($search !== '') {
            $productsQuery->where('ten_sp', 'like', '%'.$search.'%');
        }

        if ($categoryFilter !== '') {
            $productsQuery->where('loai', $categoryFilter);
        }

        $allProducts = $productsQuery->limit(1000)->get();

        // Gán KM vào từng sản phẩm (runtime)
        $promoProducts = collect();
        foreach ($allProducts as $product) {
            $bestPromo = null;
            $bestDiscount = 0;

            foreach ($activePromotions as $promo) {
                // Check if promotion can still be used (usage limits)
                if (!$promo->canBeUsed()) {
                    continue;
                }

                $discountedPrice = $promo->getDiscountedPrice($product);
                if ($discountedPrice !== null) {
                    $discount = $product->gia - $discountedPrice;
                    if ($discount > $bestDiscount) {
                        $bestDiscount     = $discount;
                        $bestPromo        = $promo;
                        $product->promo_price = $discountedPrice;
                        $product->promo       = $promo;
                    }
                }
            }

            if ($bestPromo) {
                $promoProducts->push($product);
            }
        }

        // Filter theo giá sau khuyến mãi
        if ($request->filled('min_price') && is_numeric($request->input('min_price'))) {
            $minPrice = max(0, (int) $request->input('min_price'));
            $promoProducts = $promoProducts->filter(fn ($product) => $product->promo_price >= $minPrice);
        }

        if ($request->filled('max_price') && is_numeric($request->input('max_price'))) {
            $maxPrice = max(0, (int) $request->input('max_price'));
            $promoProducts = $promoProducts->filter(fn ($product) => $product->promo_price <= $maxPrice);
        }

        // Filter theo % giảm tối thiểu
        if ($request->filled('min_discount') && is_numeric($request->input('min_discount'))) {
            $min = max(0, min(100, (int) $request->input('min_discount')));
            $promoProducts = $promoProducts->filter(function ($product) use ($min) {
                $pct = $product->gia > 0 ? (($product->gia - $product->promo_price) / $product->gia * 100) : 0;
                return $pct >= $min;
            });
        }

        // Sort
        $sort = $request->get('sort', 'discount_desc');
        $promoProducts = match($sort) {
            'price_asc'      => $promoProducts->sortBy('promo_price'),
            'price_desc'     => $promoProducts->sortByDesc('promo_price'),
            'discount_desc'  => $promoProducts->sortByDesc(fn($p) => $p->gia - $p->promo_price),
            'newest'         => $promoProducts->sortByDesc('created_at'),
            default          => $promoProducts->sortByDesc(fn($p) => $p->gia - $p->promo_price),
        };

        // Paginate manually
        $page     = $request->get('page', 1);
        $perPage  = 12;
        $total    = $promoProducts->count();
        $items    = $promoProducts->values()->slice(($page - 1) * $perPage, $perPage);

        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $items, $total, $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $categories = Product::getLoaiList();
        $bannerPromos = Promotion::currentlyActive()->orderByDesc('gia_tri')->take(3)->get();

        return view('promotions.index', compact('paginated', 'categories', 'bannerPromos', 'activePromotions'));
    }
}

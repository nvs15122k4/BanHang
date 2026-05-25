@props([
    'action',
    'categories' => [],
    'title' => 'Bộ lọc',
    'selectedCategory' => '',
    'showDiscount' => false,
    'hasFilters' => false,
    'clearUrl',
])

<aside class="col-lg-3 d-none d-lg-block">
    <div class="sidebar-filter">
        <h1 class="uix-b69612cfda">{{ $title }}</h1>

        <form method="GET" action="{{ $action }}" id="filterForm">
            @if(request()->filled('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif

            <div class="filter-section">
                <div class="filter-title">Tìm kiếm</div>
                <div class="position-relative">
                    <input type="text" name="search" class="form-control rounded-0" placeholder="Tên sản phẩm..."
                           value="{{ request('search') }}">
                    <button class="uix-8493cc9cf4" type="submit" aria-label="Tìm kiếm">
                        <i class="fas fa-search text-muted uix-9e6595fb01"></i>
                    </button>
                </div>
            </div>

            <div class="filter-section">
                <div class="filter-title">Danh mục <i class="fas fa-chevron-down uix-00313dcbd7"></i></div>
                <select name="loai_filter" class="form-control form-control-sm rounded-0">
                    <option value="">Tất cả danh mục</option>
                    @foreach($categories as $categorySlug => $categoryName)
                        <option value="{{ $categorySlug }}" {{ (string) $selectedCategory === (string) $categorySlug ? 'selected' : '' }}>
                            {{ $categoryName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-section">
                <div class="filter-title">Khoảng giá <i class="fas fa-chevron-down uix-00313dcbd7"></i></div>
                <div class="d-flex gap-2 align-items-center mb-3">
                    <input type="number" name="min_price" min="0" class="form-control form-control-sm rounded-0"
                           placeholder="Từ" value="{{ request('min_price') }}">
                    <span class="uix-8bd34921dd">-</span>
                    <input type="number" name="max_price" min="0" class="form-control form-control-sm rounded-0"
                           placeholder="Đến" value="{{ request('max_price') }}">
                </div>
            </div>

            @if($showDiscount)
                <div class="filter-section">
                    <div class="filter-title">Mức giảm tối thiểu <i class="fas fa-chevron-down uix-00313dcbd7"></i></div>
                    <select name="min_discount" class="form-control form-control-sm rounded-0 mb-3">
                        <option value="">Bất kỳ mức giảm</option>
                        @foreach([10, 20, 30, 50] as $discount)
                            <option value="{{ $discount }}" {{ (string) request('min_discount') === (string) $discount ? 'selected' : '' }}>
                                Từ {{ $discount }}% trở lên
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <button type="submit" class="btn-st-dark w-100 py-2-custom text-xs-custom">ÁP DỤNG</button>

            @if($hasFilters)
                <a href="{{ $clearUrl }}" class="btn-st-dark w-100 py-2-custom text-xs-custom text-center mt-3 d-block">XÓA BỘ LỌC</a>
            @endif
        </form>
    </div>
</aside>

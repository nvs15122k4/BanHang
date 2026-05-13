@extends('layouts.admin')

@section('title', 'Tạo đơn hàng mới')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-plus me-3"></i>TẠO ĐƠN HÀNG MỚI</h1>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>
</div>

<form method="POST" action="{{ route('admin.orders.store') }}" id="orderForm">
    @csrf
    <div class="row">
        <!-- Left: Customer & Shipping Info -->
        <div class="col-md-7">
            <div class="card admin-table mb-4">
                <div class="card-header" style="background-color: var(--text); color: #FFFFFF; font-weight: 500;">
                    <i class="fas fa-user me-2"></i>THÔNG TIN KHÁCH HÀNG
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Khách hàng <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                            <option value="">-- Chọn khách hàng --</option>
                            @foreach(\App\Models\User::orderBy('name')->get() as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên người nhận <span class="text-danger">*</span></label>
                            <input type="text" name="ten_nguoi_nhan" class="form-control @error('ten_nguoi_nhan') is-invalid @enderror"
                                   value="{{ old('ten_nguoi_nhan') }}" required>
                            @error('ten_nguoi_nhan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="sdt_nguoi_nhan" class="form-control @error('sdt_nguoi_nhan') is-invalid @enderror"
                                   value="{{ old('sdt_nguoi_nhan') }}" required>
                            @error('sdt_nguoi_nhan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                        <textarea name="dia_chi_giao_hang" class="form-control @error('dia_chi_giao_hang') is-invalid @enderror"
                                  rows="2" required>{{ old('dia_chi_giao_hang') }}</textarea>
                        @error('dia_chi_giao_hang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phương thức thanh toán <span class="text-danger">*</span></label>
                            <select name="phuong_thuc_thanh_toan" class="form-select" required>
                                <option value="cod" {{ old('phuong_thuc_thanh_toan') === 'cod' ? 'selected' : '' }}>COD - Thanh toán khi nhận</option>
                                <option value="bank_transfer" {{ old('phuong_thuc_thanh_toan') === 'bank_transfer' ? 'selected' : '' }}>Chuyển khoản ngân hàng</option>
                                <option value="vnpay" {{ old('phuong_thuc_thanh_toan') === 'vnpay' ? 'selected' : '' }}>VNPay</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ghi chú</label>
                            <input type="text" name="ghi_chu" class="form-control" value="{{ old('ghi_chu') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products -->
            <div class="card admin-table mb-4">
                <div class="card-header d-flex justify-content-between align-items-center" style="background-color: var(--text); color: #FFFFFF; font-weight: 500;">
                    <span><i class="fas fa-box me-2"></i>SẢN PHẨM</span>
                    <button type="button" class="btn btn-light btn-sm" onclick="addItem()">
                        <i class="fas fa-plus me-1"></i>Thêm sản phẩm
                    </button>
                </div>
                <div class="card-body">
                    <div id="orderItems">
                        <div class="order-item row g-2 mb-3 align-items-end" data-index="0">
                            <div class="col-md-6">
                                <label class="form-label">Sản phẩm <span class="text-danger">*</span></label>
                                <select name="items[0][product_id]" class="form-select product-select" required onchange="updatePrice(this, 0)">
                                    <option value="">-- Chọn sản phẩm --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->gia }}">
                                            {{ $product->ten_sp }} ({{ number_format($product->gia) }}đ - Còn: {{ $product->so_luong }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Số lượng <span class="text-danger">*</span></label>
                                <input type="number" name="items[0][so_luong]" class="form-control qty-input" min="1" value="1" required onchange="calculateTotal()">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Thành tiền</label>
                                <input type="text" class="form-control item-total" readonly value="0đ">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger" onclick="removeItem(this)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Summary -->
        <div class="col-md-5">
            <div class="card admin-table mb-4" style="position: sticky; top: 20px;">
                <div class="card-header" style="background-color: var(--text); color: #FFFFFF; font-weight: 500;">
                    <i class="fas fa-receipt me-2"></i>TỔNG KẾT ĐƠN HÀNG
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td>Tổng tiền hàng:</td>
                            <td class="text-end fw-bold" id="summaryTotal">0đ</td>
                        </tr>
                        <tr>
                            <td>Phí vận chuyển:</td>
                            <td class="text-end">0đ</td>
                        </tr>
                        <tr>
                            <td>Giảm giá:</td>
                            <td class="text-end">0đ</td>
                        </tr>
                        <tr class="border-top">
                            <td><strong>TỔNG THANH TOÁN:</strong></td>
                            <td class="text-end"><strong class="text-success fs-5" id="summaryFinal">0đ</strong></td>
                        </tr>
                    </table>
                    <hr>
                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                        <i class="fas fa-check me-2"></i>TẠO ĐƠN HÀNG
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
let itemIndex = 1;
const products = @json($products->map(fn($p) => ['id' => $p->id, 'name' => $p->ten_sp, 'price' => $p->gia, 'stock' => $p->so_luong]));

function addItem() {
    const container = document.getElementById('orderItems');
    const html = `
        <div class="order-item row g-2 mb-3 align-items-end" data-index="${itemIndex}">
            <div class="col-md-6">
                <label class="form-label">Sản phẩm <span class="text-danger">*</span></label>
                <select name="items[${itemIndex}][product_id]" class="form-select product-select" required onchange="updatePrice(this, ${itemIndex})">
                    <option value="">-- Chọn sản phẩm --</option>
                    ${products.map(p => `<option value="${p.id}" data-price="${p.price}">${p.name} (${p.price.toLocaleString('vi-VN')}đ - Còn: ${p.stock})</option>`).join('')}
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Số lượng <span class="text-danger">*</span></label>
                <input type="number" name="items[${itemIndex}][so_luong]" class="form-control qty-input" min="1" value="1" required onchange="calculateTotal()">
            </div>
            <div class="col-md-2">
                <label class="form-label">Thành tiền</label>
                <input type="text" class="form-control item-total" readonly value="0đ">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger" onclick="removeItem(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>`;
    container.insertAdjacentHTML('beforeend', html);
    itemIndex++;
}

function removeItem(btn) {
    const items = document.querySelectorAll('.order-item');
    if (items.length <= 1) { showToast('Đơn hàng phải có ít nhất 1 sản phẩm!', 'warning'); return; }
    btn.closest('.order-item').remove();
    calculateTotal();
}

function updatePrice(select, index) {
    const option = select.options[select.selectedIndex];
    const price = parseFloat(option.dataset.price) || 0;
    const row = select.closest('.order-item');
    const qty = parseInt(row.querySelector('.qty-input').value) || 1;
    const total = price * qty;
    row.querySelector('.item-total').value = total.toLocaleString('vi-VN') + 'đ';
    calculateTotal();
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.order-item').forEach(row => {
        const select = row.querySelector('.product-select');
        const option = select.options[select.selectedIndex];
        const price = parseFloat(option?.dataset?.price) || 0;
        const qty = parseInt(row.querySelector('.qty-input').value) || 0;
        const itemTotal = price * qty;
        row.querySelector('.item-total').value = itemTotal.toLocaleString('vi-VN') + 'đ';
        total += itemTotal;
    });
    document.getElementById('summaryTotal').textContent = total.toLocaleString('vi-VN') + 'đ';
    document.getElementById('summaryFinal').textContent = total.toLocaleString('vi-VN') + 'đ';
}
</script>
@endpush
@endsection

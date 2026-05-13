@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-shopping-cart me-3"></i>ĐƠN HÀNG #{{ $order->ma_don_hang }}</h1>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>
</div>

<div class="row">
    <!-- Order Info -->
    <div class="col-md-8">
        <div class="card admin-table mb-4">
            <div class="card-header">
                <i class="fas fa-info-circle me-2"></i>THÔNG TIN ĐƠN HÀNG
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Mã đơn hàng:</strong> {{ $order->ma_don_hang }}
                    </div>
                    <div class="col-md-6">
                        <strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Khách hàng:</strong> {{ $order->user->name }}<br>
                        <small class="text-muted">{{ $order->user->email }}</small>
                    </div>
                    <div class="col-md-6">
                        <strong>Người nhận:</strong> {{ $order->ten_nguoi_nhan }}<br>
                        <small class="text-muted">{{ $order->sdt_nguoi_nhan }}</small>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <strong>Địa chỉ giao hàng:</strong><br>
                        {{ $order->dia_chi_giao_hang }}
                    </div>
                </div>
                @if($order->ghi_chu)
                <div class="row">
                    <div class="col-12">
                        <strong>Ghi chú:</strong><br>
                        {{ $order->ghi_chu }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Order Items -->
        <div class="card admin-table mb-4">
            <div class="card-header">
                <i class="fas fa-box me-2"></i>SẢN PHẨM
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead style="background-color: var(--bg);">
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                            <tr>
                                <td style="font-weight: 500;">{{ $item->ten_san_pham }}</td>
                                <td>{{ number_format($item->gia) }}đ</td>
                                <td>{{ $item->so_luong }}</td>
                                <td style="font-weight: 500;">{{ number_format($item->thanh_tien) }}đ</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background-color: var(--bg);">
                        <tr>
                            <td colspan="3" class="text-end"><strong>Tổng tiền hàng:</strong></td>
                            <td><strong>{{ number_format($order->tong_tien) }}đ</strong></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Phí vận chuyển:</strong></td>
                            <td><strong>{{ number_format($order->phi_van_chuyen) }}đ</strong></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Giảm giá:</strong></td>
                            <td><strong class="text-danger">-{{ number_format($order->giam_gia) }}đ</strong></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>TỔNG THANH TOÁN:</strong></td>
                            <td><strong class="text-success fs-5">{{ number_format($order->thanh_tien) }}đ</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Status & Actions -->
    <div class="col-md-4">
        <!-- Status Card -->
        <div class="card admin-table mb-4">
            <div class="card-header">
                <i class="fas fa-tasks me-2"></i>TRẠNG THÁI
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Trạng thái đơn hàng:</strong><br>
                    @php
                        $statusColors = [
                            'pending'   => 'warning',
                            'confirmed' => 'info',
                            'shipping'  => 'primary',
                            'delivered' => 'warning',
                            'disputing' => 'danger',
                            'completed' => 'success',
                            'cancelled' => 'danger',
                        ];
                        $color = $statusColors[$order->trang_thai] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $color }} fs-6 mt-2">{{ $order->status_label }}</span>
                </div>

                @php
                    $statusLabels  = \App\Models\Order::adminStatusLabels();
                @endphp

                <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="mb-3">
                    @csrf
                    @method('PUT')
                    <label class="form-label"><strong>Chuyển trạng thái:</strong></label>
                    <select name="trang_thai" class="form-select mb-2" required>
                        @foreach($statusLabels as $val => $label)
                            <option value="{{ $val }}" {{ $order->trang_thai === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i>Cập nhật
                    </button>
                </form>

                <hr>

                <div class="mb-3">
                    <strong>Trạng thái thanh toán:</strong><br>
                    <span class="badge bg-{{ $order->trang_thai_thanh_toan === 'paid' ? 'success' : 'secondary' }} fs-6 mt-2">
                        {{ $order->payment_status_label }}
                    </span>
                </div>

                <form method="POST" action="{{ route('admin.orders.payment', $order) }}">
                    @csrf
                    @method('PUT')
                    <label class="form-label"><strong>Cập nhật thanh toán:</strong></label>
                    <select name="trang_thai_thanh_toan" class="form-select mb-2" required>
                        <option value="unpaid" {{ $order->trang_thai_thanh_toan === 'unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
                        <option value="paid" {{ $order->trang_thai_thanh_toan === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                    </select>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-money-bill me-2"></i>Cập nhật
                    </button>
                </form>

                <hr>

                <div>
                    <strong>Phương thức thanh toán:</strong><br>
                    {{ $order->payment_method_label }}
                </div>
            </div>
        </div>

        <!-- Inventory Logs -->
        @if($order->inventoryLogs->count() > 0)
        <div class="card admin-table">
            <div class="card-header">
                <i class="fas fa-warehouse me-2"></i>LỊCH SỬ KHO
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tbody>
                        @foreach($order->inventoryLogs as $log)
                            <tr>
                                <td>
                                    <small>
                                        <strong>{{ $log->product->ten_sp }}</strong><br>
                                        {{ $log->type_label }}: {{ $log->so_luong_thay_doi > 0 ? '+' : '' }}{{ $log->so_luong_thay_doi }}<br>
                                        <span class="text-muted">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                                    </small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

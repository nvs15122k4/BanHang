@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng - ' . $order->id)
@section('robots', 'noindex, nofollow')

@push('styles')
    @vite(['public/css/views/order_show.css'])
@endpush

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">ĐƠN HÀNG #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-dark rounded-0">VỀ DANH SÁCH</a>
    </div>

    <div class="row">
        <!-- Main Info -->
        <div class="col-lg-8">
            
        <!-- Delivery Details -->
            <div class="order-section">
                <div class="section-header">
                    <i class="fas fa-map-marker-alt"></i> THÔNG TIN GIAO HÀNG
                </div>
                <div class="section-body">
                    <div class="row">
                        <div class="col-6 mb-4">
                            <div class="info-label">Người nhận</div>
                            <div class="info-value">{{ $order->ten_nguoi_nhan }}</div>
                            <div class="info-label">Địa chỉ</div>
                            <div class="info-value">{{ $order->dia_chi_giao_hang }}</div>
                        </div>
                        <div class="col-6 mb-4">
                            <div class="info-label">Số điện thoại</div>
                            <div class="info-value">{{ $order->sdt_nguoi_nhan }}</div>
                            @if($order->ghi_chu)
                            <div class="info-label">Ghi chú</div>
                            <div class="info-value">{{ $order->ghi_chu }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- Order Status -->
            <div class="order-section">
                <div class="section-header">
                    <i class="fas fa-truck"></i> TRẠNG THÁI ĐƠN HÀNG
                </div>
                <div class="section-body">
                    @if($order->trang_thai === 'cancelled')
                        <div class="cancelled-state">
                            <i class="fas fa-times-circle fa-4x text-danger mb-3"></i>
                            <h4 class="font-bold uix-352917a2be">Đơn hàng đã hủy</h4>
                            <p class="text-muted">Đơn hàng này đã bị hủy và sẽ không được xử lý.</p>
                        </div>
                    @else
                        @php
                            $steps = [
                                ['key' => 'pending',   'icon' => 'clipboard-list', 'label' => 'Chờ duyệt'],
                                ['key' => 'confirmed', 'icon' => 'box',            'label' => 'Chuẩn bị hàng'],
                                ['key' => 'shipping',  'icon' => 'shipping-fast',  'label' => 'Đang giao'],
                                ['key' => 'delivered', 'icon' => 'user-check',     'label' => 'Đã giao'],
                                ['key' => 'completed', 'icon' => 'box-open',       'label' => 'Hoàn thành'],
                            ];

                            // disputing vẫn nằm ở bước delivered trên track
                            $trackKey   = $order->trang_thai === 'disputing' ? 'delivered' : $order->trang_thai;
                            $stepKeys   = array_column($steps, 'key');
                            $currentIdx = array_search($trackKey, $stepKeys) ?? 0;
                        @endphp

                        <div class="status-track">
                            {{-- Line fill --}}
                            <div class="status-track-fill" style="width: {{ ($currentIdx / (count($steps) - 1)) * 100 }}%;"></div>
                            
                            @foreach($steps as $i => $step)
                                <div class="status-step {{ $i <= $currentIdx ? 'active' : '' }}">
                                    <div class="status-icon"><i class="fas fa-{{ $step['icon'] }}"></i></div>
                                    <div class="status-label">{{ $step['label'] }}</div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Banner trạng thái đặc biệt --}}
                        @if($order->trang_thai === 'disputing')
                            <div class="text-center mt-3 py-3 uix-d8da30a21b">
                                <i class="fas fa-exclamation-circle fa-2x mb-2 uix-ad41fddcc4"></i>
                                <p class="mb-0 font-bold uix-ad41fddcc4">Đang xử lý khiếu nại — chúng tôi sẽ liên hệ bạn sớm nhất</p>
                            </div>
                        @elseif($order->trang_thai === 'cancelling')
                            <div class="text-center mt-3 py-3 uix-d8da30a21b">
                                <i class="fas fa-clock fa-2x mb-2 uix-ad41fddcc4"></i>
                                <p class="mb-0 font-bold uix-ad41fddcc4">Yêu cầu hủy đơn hàng đang được xử lý — chúng tôi sẽ phản hồi sớm nhất</p>
                            </div>
                        @endif
                        {{-- Nút hành động của user khi đơn đang giao --}}
                        @php $userNext = \App\Models\Order::userNextStatuses($order->trang_thai); @endphp
                        @if(count($userNext) > 0)
                            <div class="mt-4 p-3 uix-fa8f0cdd6a">
                                <p class="font-bold mb-3 text-sm-custom">Bạn đã nhận được hàng chưa?</p>
                                <div class="d-flex gap-2 flex-wrap">
                                    {{-- Đã nhận --}}
                                    <form class="uix-0cd28ce9ba" method="POST" action="{{ route('orders.updateStatus', $order) }}" data-item-name="#{{ $order->ma_don_hang }}">
                                        @csrf
                                        <input type="hidden" name="trang_thai" value="completed">
                                        <button type="submit" class="btn btn-success rounded-0 font-bold tracking-wide-custom uix-d83ad4db53"
                                           
                                            onclick="return confirmForm(this.form, 'Xác nhận bạn đã nhận được hàng? Trạng thái đơn hàng sẽ chuyển thành Hoàn thành.', 'Xác nhận đã nhận được hàng', 'success', 'Xác nhận đã nhận')">
                                            <i class="fas fa-check-circle me-2"></i>ĐÃ NHẬN ĐƯỢC HÀNG
                                        </button>
                                    </form>
                                    {{-- Chưa nhận / Khiếu nại --}}
                                    <form class="uix-0cd28ce9ba" method="POST" action="{{ route('orders.updateStatus', $order) }}" data-item-name="#{{ $order->ma_don_hang }}">
                                        @csrf
                                        <input type="hidden" name="trang_thai" value="disputing">
                                        <button type="submit" class="btn btn-outline-danger rounded-0 font-bold tracking-wide-custom uix-d83ad4db53"
                                           
                                            onclick="return confirmForm(this.form, 'Xác nhận bạn chưa nhận được hàng và muốn khiếu nại? Chúng tôi sẽ tiếp nhận và xử lý sớm nhất.', 'Xác nhận gửi khiếu nại', 'danger', 'Gửi khiếu nại')">
                                            <i class="fas fa-exclamation-circle me-2"></i>CHƯA NHẬN ĐƯỢC HÀNG
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- Banner thông tin hoàn tiền --}}
                    @if($order->refund_status !== 'none')
                        <div class="mt-4 p-4 uix-b42cc52776">
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <i class="fas fa-info-circle fa-2x uix-b062634876"></i>
                                <div class="uix-7623f05545">
                                    <h6 class="mb-1 fw-bold uix-e5984d47c6">Thông tin hoàn tiền</h6>
                                    <p class="mb-0 text-muted small">Đơn hàng này đủ điều kiện hoàn tiền. Vui lòng kiểm tra và cung cấp thông tin bên dưới.</p>
                                </div>
                                @if($order->refund_status === 'completed')
                                    <span class="badge bg-success rounded-0 px-3 py-2">ĐÃ HOÀN TIỀN</span>
                                @endif
                            </div>

                            @if($order->refund_status === 'pending' && !$order->refund_bank_name)
                                <form action="{{ route('orders.submitRefund', $order) }}" method="POST" class="bg-white p-3 border">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted">Số tiền hoàn trả</label>
                                            <input type="text" class="form-control form-control-sm bg-light fw-bold" value="{{ number_format($order->thanh_tien) }}đ" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted">Tên ngân hàng</label>
                                            <input type="text" name="refund_bank_name" class="form-control form-control-sm rounded-0" placeholder="Ví dụ: Vietcombank..." required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted">Số tài khoản</label>
                                            <input type="text" name="refund_account_number" class="form-control form-control-sm rounded-0" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted">Tên chủ tài khoản</label>
                                            <input type="text" name="refund_account_name" class="form-control form-control-sm rounded-0" placeholder="NGUYEN VAN A" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-bold text-muted">Ghi chú (nếu có)</label>
                                            <textarea name="refund_user_note" class="form-control form-control-sm rounded-0" rows="2"></textarea>
                                        </div>
                                        <div class="col-12 text-end">
                                            <button type="submit" class="btn btn-dark btn-sm px-4 rounded-0 font-bold">GỬI THÔNG TIN</button>
                                        </div>
                                    </div>
                                </form>
                            @elseif($order->refund_status === 'pending')
                                <div class="bg-white p-3 border">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <div class="small text-muted">Số tiền:</div>
                                            <div class="fw-bold">{{ number_format($order->thanh_tien) }}đ</div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <div class="small text-muted">Ngân hàng:</div>
                                            <div class="fw-bold">{{ $order->refund_bank_name }}</div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <div class="small text-muted">Số tài khoản:</div>
                                            <div class="fw-bold">{{ $order->refund_account_number }}</div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <div class="small text-muted">Chủ tài khoản:</div>
                                            <div class="fw-bold">{{ $order->refund_account_name }}</div>
                                        </div>
                                    </div>
                                    <div class="alert alert-warning py-2 mb-0 mt-2 rounded-0 small">
                                        <i class="fas fa-clock me-2"></i>Thông tin đã được gửi. Đang chờ Admin thực hiện chuyển khoản.
                                    </div>
                                </div>
                            @else
                                <div class="bg-white p-3 border">
                                    <div class="alert alert-success py-2 mb-0 rounded-0 small">
                                        <i class="fas fa-check-circle me-2"></i>Tiền đã được hoàn về tài khoản của bạn.
                                    </div>
                                    @if($order->refund_admin_note)
                                        <div class="mt-2 small text-muted"><strong>Ghi chú từ Admin:</strong> {{ $order->refund_admin_note }}</div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Ordered Items -->
            <div class="order-section">
                <div class="section-header">
                    <i class="fas fa-box"></i> SẢN PHẨM ĐÃ ĐẶT
                </div>
                <div class="section-body">
                    @foreach($order->orderItems as $detail)
                        <div class="order-item">
                            @if($detail->product && $detail->product->anh)
                                <img src="{{ $detail->product->image_path }}" class="item-img" alt="Product">
                            @else
                                <div class="item-img d-flex align-items-center justify-content-center border">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            @endif
                            
                            <div class="uix-7623f05545">
                                <div class="item-title">
                                    @if($detail->product)
                                        <a href="{{ route('products.show', ['product' => $detail->product->slug]) }}" class="inline-link-inherit">
                                            {{ $detail->product->ten_sp }}
                                        </a>
                                    @else
                                        Sản phẩm đã xóa
                                    @endif
                                </div>
                                <div class="item-meta">{{ number_format($detail->gia) }}đ × {{ $detail->so_luong }}
                                    @if($detail->size && $detail->size !== 'default')
                                        <span class="ms-2 badge bg-secondary">{{ $detail->size }}</span>
                                    @endif
                                </div>

                                {{-- Nút đánh giá — chỉ hiện khi đơn hoàn thành và còn sản phẩm --}}
                                @if($order->trang_thai === 'completed' && $detail->product)
                                    @php
                                        $reviewed = \App\Models\Review::where('user_id', auth()->id())
                                            ->where('product_id', $detail->product->id)
                                            ->first();
                                    @endphp
                                    @if($reviewed)
                                        <button type="button"
                                           class="btn mt-2 uix-390926d2bb"
                                          
                                           data-bs-toggle="modal"
                                           data-bs-target="#globalReviewModal"
                                           data-product-id="{{ $detail->product->id }}"
                                           data-product-name="{{ $detail->product->ten_sp }}"
                                           data-product-image="{{ $detail->product->image_path }}"
                                           data-product-price="{{ number_format($detail->gia) }}đ"
                                           data-review="{{ $reviewed->toJson() }}">
                                            <i class="fas fa-check-circle me-1"></i>Xem đánh giá
                                        </button>
                                    @else
                                        <button type="button"
                                           class="btn mt-2 uix-2175ed6068"
                                          
                                           data-bs-toggle="modal"
                                           data-bs-target="#globalReviewModal"
                                           data-product-id="{{ $detail->product->id }}"
                                           data-product-name="{{ $detail->product->ten_sp }}"
                                           data-product-image="{{ $detail->product->image_path }}"
                                           data-product-price="{{ number_format($detail->gia) }}đ">
                                            <i class="fas fa-star me-1"></i>Đánh giá
                                        </button>
                                    @endif
                                @endif
                            </div>
                            
                            <div class="fw-bold uix-b87efa5bc3">
                                {{ number_format($detail->thanh_tien) }}đ
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="mt-4 pt-3 border-top">
                        <div class="summary-row">
                            <span>Tạm tính:</span>
                            <span class="fw-bold uix-83b020b78b">{{ number_format($order->tong_tien) }}đ</span>
                        </div>
                        @if($order->giam_gia > 0)
                        <div class="summary-row text-danger">
                            <span>Khuyến mãi:</span>
                            <span class="fw-bold">-{{ number_format($order->giam_gia) }}đ</span>
                        </div>
                        @endif
                        <div class="summary-row">
                            <span>Vận chuyển:</span>
                            <span class="fw-bold uix-5fc6ab3c76">Miễn phí</span>
                        </div>
                        <div class="summary-total">
                            <span>Tổng tiền:</span>
                            <span>{{ number_format($order->thanh_tien) }}đ</span>
                        </div>
                    </div>
                </div>
            </div>
            @if($order->trang_thai === 'pending')
            <div class="mt-4 p-3 text-center uix-d877e28199">
                <p class="mb-3 uix-b0973446ac">Bạn muốn hủy đơn hàng này?</p>
                <form method="POST" action="{{ route('orders.cancel', $order) }}" data-order-code="{{ $order->ma_don_hang }}" data-confirm-text="Xác nhận hủy" onsubmit="return confirmForm(this, 'Xác nhận hủy đơn hàng này?', 'HỦY ĐƠN HÀNG')">
                @csrf
                <button type="submit" class="btn btn-danger rounded-0 font-bold tracking-wide-custom uix-d83ad4db53" 
               >
                <i class="fas fa-times-circle me-2"></i>Hủy đơn hàng
                </button>
                </form>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Payment Details -->
            <div class="order-section">
                <div class="section-header">
                    <i class="fas fa-credit-card"></i> THÔNG TIN THANH TOÁN
                </div>
                <div class="section-body">
                    <div class="info-label">Phương thức</div>
                    <div class="info-value">
                        {{ $order->payment_method_label }}
                    </div>
                    
                    <div class="info-label">Trạng thái thanh toán</div>
                    <div>
                        @if($order->trang_thai_thanh_toan === \App\Models\Order::PAYMENT_PAID)
                            <span class="badge-paid">{{ $order->payment_status_label }}</span>
                        @else
                            <span class="badge-unpaid">{{ $order->payment_status_label }}</span>
                        @endif
                    </div>

                    @if($order->phuong_thuc_thanh_toan === 'vietqr' && in_array($order->trang_thai_thanh_toan, [\App\Models\Order::PAYMENT_PENDING, \App\Models\Order::PAYMENT_UNPAID], true))
                        <div class="mt-4 pt-4 border-top text-center">
                            <div class="info-label mb-3 text-center">Quét mã QR để thanh toán</div>
                            <img src="{{ $order->vietqr_url }}" alt="VietQR Code" class="img-fluid border p-2 bg-white mb-2 inline-vietqr-image">
                            <p class="text-muted small mt-2">Sử dụng App ngân hàng của bạn để quét mã này.<br>Nội dung chuyển khoản: <strong>{{ $order->ma_don_hang }}</strong></p>
                        </div>
                    @endif
                </div>
            </div>
            <!-- Payment Copy -->
            <div class="order-section">
                <div class="section-header">SAO CHÉP THÔNG TIN THANH TOÁN
                </div>
                <div class="section-body">
                    <div class="info-label">Số tài khoản</div>
                    <div class="info-value" id="stk" onclick="copyToClipboard(document.getElementById('stk').innerText)">1014232408 <i class="fas fa-copy"></i></div>
                    <div class="info-label">Chủ tài khoản</div>
                    <div class="info-value">Nguyễn Văn Sang</div>
                    <div class="info-label">Ngân hàng</div>
                    <div class="info-value">Vietcombank</div>
                    <div class="info-label">Số tiền</div>
                    <div class="info-value" id="amount" onclick="copyToClipboard(document.getElementById('amount').innerText.replace(/\D/g, ''))">{{ number_format($order->thanh_tien) }}đ  <i class="fas fa-copy"></i></div>
                    <div class="info-label">Nội dung</div>
                    <div class="info-value uix-d3612f07e0" id="content" onclick="copyToClipboard(document.getElementById('content').innerText)">{{ $order->ma_don_hang }} <i class="fas fa-copy"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="copyToast">Đã sao chép!</div>

<script>
    function copyToClipboard(text) {
        // Clean text (remove icons and extra whitespace)
        text = text.replace(/<[^>]*>?/gm, '').trim();
        
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);

        // Show simple toast
        const toast = document.getElementById('copyToast');
        toast.style.display = 'block';
        
        // Reset animation
        toast.style.animation = 'none';
        toast.offsetHeight; // trigger reflow
        toast.style.animation = null;

        setTimeout(() => {
            toast.style.display = 'none';
        }, 2000);
    }
</script>
@endsection

# Hướng Dẫn Sử Dụng Hệ Thống BanHang

Tài liệu này hướng dẫn các thao tác cơ bản để sử dụng và vận hành website bán hàng thời trang **BanHang**.

## 1. Dành cho Khách Hàng (Người Mua)

### Mua sắm và Đặt hàng
- **Đăng ký / Đăng nhập**: Khách hàng có thể tạo tài khoản và xác nhận email để quản lý đơn hàng tốt hơn.
- **Xem và Tìm kiếm**: Sử dụng thanh tìm kiếm hoặc bộ lọc theo danh mục, giá cả để tìm sản phẩm mong muốn.
- **Xem chi tiết & Gợi ý size**: Trong trang chi tiết sản phẩm, khách hàng có thể nhập chiều cao và cân nặng để hệ thống tự động gợi ý kích cỡ (size) phù hợp nhất.
- **Giỏ hàng**: Thêm sản phẩm, cập nhật số lượng hoặc xóa sản phẩm khỏi giỏ.
- **Thanh toán**: Cập nhật địa chỉ giao hàng và xác nhận đặt hàng để hoàn tất.

### Quản lý Tài Khoản & Đơn Hàng
- **Theo dõi đơn hàng**: Xem tình trạng xử lý đơn hàng của bạn.
- **Yêu cầu hủy / hoàn tiền**: Gửi yêu cầu hủy đơn nếu đơn hàng chưa được giao, hoặc cung cấp thông tin hoàn tiền.
- **Wishlist & Đánh giá**: Lưu trữ các sản phẩm yêu thích và để lại đánh giá, nhận xét cho sản phẩm đã mua.

---

## 2. Dành cho Quản Trị Viên (Admin)

### Thông tin đăng nhập mặc định (Môi trường phát triển)
- **Email**: `admin@gmail.com`
- **Mật khẩu**: `123456`

### Các Thao Tác Quản Trị Chính
- **Dashboard**: Xem tổng quan về các hoạt động của hệ thống, thống kê doanh thu và đơn hàng mới.
- **Quản lý Sản phẩm & Danh mục**: 
  - Tạo mới, chỉnh sửa, xóa danh mục sản phẩm.
  - Quản lý thông tin chi tiết sản phẩm, hình ảnh, phân loại size, màu sắc và trạng thái hiển thị.
- **Quản lý Đơn hàng**: 
  - Duyệt danh sách đơn đặt hàng từ khách hàng.
  - Cập nhật tiến độ giao hàng và trạng thái thanh toán.
  - Phê duyệt hoặc từ chối các yêu cầu hủy đơn, hoàn tiền.
- **Quản lý Kho hàng**:
  - Lập phiếu nhập kho, xuất kho hoặc điều chỉnh lượng tồn kho.
  - Theo dõi lịch sử biến động kho hàng.
- **Khuyến mãi & Báo cáo**: Tạo chương trình khuyến mãi và xuất báo cáo, danh sách sản phẩm ra file Excel.

---

## 3. Khởi Chạy Hệ Thống (Dành cho Developer/Admin)

Để trải nghiệm các tính năng trên, bạn có thể chạy hệ thống trên máy tính local.

### Chạy bằng Docker (Khuyên dùng)
1. Tạo file cấu hình: `cp .env.docker.example .env.docker`
2. Khởi động Docker: `docker compose --env-file .env.docker up -d --build`
3. Chạy migration và tạo dữ liệu mẫu: `docker compose --env-file .env.docker exec app php artisan migrate --seed`
4. Truy cập web: **http://localhost:8080**

### Chạy thủ công
1. Cài đặt các gói phụ thuộc:
   ```bash
   composer install
   npm install
   ```
2. Cấu hình môi trường:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *(Nhớ cấu hình kết nối Database trong file `.env`)*
3. Tạo dữ liệu mẫu và build frontend:
   ```bash
   php artisan migrate --seed
   npm run build
   ```
4. Khởi động server: `php artisan serve`
5. Truy cập web: **http://127.0.0.1:8000**

> **CẢNH BÁO:** Không chạy lệnh `migrate:fresh` hay `db:seed` trên hệ thống đang chạy thật (Production) để tránh bị mất toàn bộ dữ liệu.

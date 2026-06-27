# BÁO CÁO THỰC TẬP TỐT NGHIỆP - PHẦN 3
## (Trang 21-30: CHƯƠNG 2 & 3 - DỰ ÁN VÀ PLAYWRIGHT)

---

## CHƯƠNG 2: GIỚI THIỆU DỰ ÁN SÀN TÍM VI EN

### 2.1 Mô tả dự án

Sàn Tím Vi En là một nền tảng thương mại điện tử được xây dựng trên Laravel framework. Dự án này nhằm cung cấp một giải pháp hoàn chỉnh cho bán hàng trực tuyến, cho phép các nhà bán hàng hiển thị sản phẩm, quản lý đơn hàng, và khách hàng có thể mua hàng một cách dễ dàng và an toàn.

Tên dự án "Sàn Tím Vi En" được lấy cảm hứng từ ý tưởng tạo một "sàn giao dịch" (marketplace) mà "tím" tượng trưng cho sự độc đáo, và "Vi En" là viết tắt của "Vietnam Enterprise". Nói cách khác, dự án này là một nền tảng mua bán điện tử dành cho các doanh nghiệp Việt Nam.

### 2.2 Công nghệ sử dụng

**Backend:**
- Laravel Framework (PHP): Một framework PHP mạnh mẽ, được sử dụng rộng rãi cho phát triển web
- MySQL Database: Cơ sở dữ liệu quan hệ, lưu trữ tất cả dữ liệu của hệ thống
- RESTful API: Cung cấp các endpoint API để frontend và mobile app sử dụng

**Frontend:**
- Vue.js: Framework JavaScript progressve dùng để xây dựng giao diện người dùng
- Vite: Build tool hiện đại, nhanh hơn Webpack
- Tailwind CSS: Framework CSS utility-first, tạo giao diện đẹp

**Testing:**
- Playwright (E2E Testing): Framework kiểm thử tự động end-to-end
- Postman (API Testing): Công cụ kiểm thử API

### 2.3 Phạm vi chức năng

Dự án bao gồm các chức năng chính:

1. **Xác thực người dùng**
   - Đăng nhập với email/username
   - Đăng xuất
   - Đăng ký tài khoản mới
   - Quên mật khẩu, đặt lại mật khẩu
   - Xác thực 2 yếu tố (2FA)

2. **Quản lý sản phẩm**
   - Xem danh sách sản phẩm
   - Xem chi tiết sản phẩm
   - Tìm kiếm và lọc sản phẩm
   - Xem nhận xét, đánh giá sản phẩm
   - Yêu thích sản phẩm

3. **Giỏ hàng**
   - Thêm sản phẩm vào giỏ
   - Xoá sản phẩm khỏi giỏ
   - Cập nhật số lượng sản phẩm
   - Xem tổng giá trị giỏ hàng

4. **Thanh toán**
   - Quy trình checkout
   - Chọn địa chỉ giao hàng
   - Chọn phương thức thanh toán (thẻ tín dụng, ví điện tử, COD)
   - Nhập thông tin thanh toán
   - Xác nhận đơn hàng

5. **Quản lý đơn hàng**
   - Xem lịch sử đơn hàng
   - Xem chi tiết đơn hàng
   - Theo dõi trạng thái giao hàng
   - Hủy đơn hàng
   - Trả hàng, hoàn tiền

6. **Quản lý tài khoản**
   - Xem hồ sơ người dùng
   - Cập nhật thông tin cá nhân
   - Thay đổi mật khẩu
   - Quản lý địa chỉ giao hàng
   - Xem lịch sử mua hàng

7. **Quản lý Admin**
   - Dashboard hiển thị thống kê
   - Quản lý sản phẩm (thêm, sửa, xoá)
   - Quản lý đơn hàng (xem, cập nhật trạng thái)
   - Quản lý người dùng
   - Báo cáo và thống kê

[Hình: Kiến trúc hệ thống Sàn Tím Vi En, Giao diện trang chủ, Giao diện chi tiết sản phẩm, Giao diện giỏ hàng]

---

## CHƯƠNG 3: TỔNG QUAN VỀ PLAYWRIGHT VÀ KIỂM THỬ TỰ ĐỘNG

### 3.1 Giới thiệu Playwright

Playwright là một framework kiểm thử tự động mạnh mẽ được phát triển bởi Microsoft. Nó cho phép các nhà kiểm thử viết các test case tự động để kiểm thử các ứng dụng web trên nhiều trình duyệt khác nhau (Chromium, Firefox, WebKit) và cung cấp các API mạnh mẽ để tương tác với các ứng dụng web.

Playwright được viết bằng TypeScript và cung cấp các binding cho JavaScript, Python, .NET, và Java. Đối với dự án này, chúng ta sử dụng TypeScript vì nó cung cấp type safety và tốt hơn cho việc bảo trì code.

### 3.2 Lợi ích của Playwright

**1. Đa trình duyệt (Multi-browser support)**
- Hỗ trợ kiểm thử trên Chromium, Firefox, WebKit
- Các test case được viết một lần có thể chạy trên tất cả các trình duyệt
- Giảm công sức viết test code

**2. Hiệu suất cao (High performance)**
- Execution engine tối ưu, test chạy nhanh
- Parallel execution: có thể chạy nhiều test cùng lúc
- Giảm thời gian chạy bộ test

**3. API mạnh mẽ (Powerful API)**
- Cung cấp các API dễ sử dụng để interact với web
- Hỗ trợ các hành động phức tạp: drag & drop, file upload, etc.
- Network interception: có thể mock API responses

**4. Kỹ năng chờ thông minh (Smart waiting)**
- Tự động chờ các phần tử xuất hiện trước khi tương tác
- Không cần explicit wait hay sleep
- Giảm flakiness của test

**5. Debugging mạnh mẽ (Powerful debugging)**
- Playwright Inspector: giao diện debug trực quan
- Video recording của các test failures
- Screenshot: tự động capture screenshot khi test fail

**6. CI/CD Integration dễ dàng**
- Tích hợp dễ dàng với GitHub Actions, Jenkins, GitLab CI
- Cung cấp Docker images cho các test environment
- Hỗ trợ parallel execution trong CI/CD

### 3.3 So sánh Playwright với các công cụ khác

| Tiêu chí | Playwright | Selenium | Cypress |
|----------|-----------|----------|---------|
| Đa trình duyệt | ✓ (Chromium, Firefox, WebKit) | ✓ (Chrome, Firefox, Safari, Edge) | ✗ (Chủ yếu Chrome) |
| Hiệu suất | Cao | Trung bình | Cao |
| Ngôn ngữ | TS/JS, Python, .NET, Java | Java, Python, C#, Ruby | JS/TS |
| Debugging | Tốt (Inspector, Video, Screenshots) | Bình thường | Rất tốt |
| API | Đơn giản, hiện đại | Phức tạp, cũ | Đơn giản |
| Learning curve | Dễ | Khó | Dễ |
| Community | Tăng nhanh | Lớn, lâu đời | Lớn |
| Mobile testing | ✓ | ✗ | ✗ |
| API Mocking | ✓ | ✗ | ✓ |

### 3.4 Kiến trúc Playwright

Playwright hoạt động theo mô hình Client-Server:

**Browser Protocol (DevTools Protocol):**
- Kết nối giữa Playwright client và browser thực
- Giao tiếp qua WebSocket
- Cho phép Playwright điều khiển browser remotely

**Automation Engine:**
- Xử lý các lệnh từ test script
- Tương tác với browser qua DevTools Protocol
- Quản lý browser context, page, frame, element

**Test Runner:**
- Chạy các test case, thu thập kết quả
- Báo cáo test results
- Tích hợp với các CI/CD system

### 3.5 Tại sao chọn Playwright cho dự án này

**1. Tương thích với công nghệ của dự án**
- Frontend được xây dựng bằng Vue.js, một framework JavaScript modern
- Playwright có support tốt cho các modern web frameworks

**2. Hiệu suất**
- Test chạy nhanh, tối ưu hóa time-to-feedback cho developers

**3. Dễ sử dụng**
- API đơn giản, dễ học
- Tốt cho beginners và professionals

**4. Tính linh hoạt**
- Hỗ trợ kiểm thử trên nhiều trình duyệt
- Hỗ trợ headless mode cho CI/CD
- Hỗ trợ parallel execution

**5. Community và support**
- Community lớn, tài liệu tốt
- Được phát triển bởi Microsoft, một công ty lớn

[Hình: Logo Playwright, Architecture diagram, Comparison chart]

---

**Tiếp tục ở Phần 4...**

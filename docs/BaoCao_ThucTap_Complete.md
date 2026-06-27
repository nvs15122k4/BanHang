# BÁO CÁO THỰC TẬP TỐT NGHIỆP

**XÂY DỰNG VÀ KIỂM THỬ WEBSITE BÁN HÀNG SÀN TÍM VI EN ỨNG DỤNG PLAYWRIGHT VÀ POSTMAN**

---

## THÔNG TIN CƠ BẢN

- **Sinh viên**: Nguyễn Văn Sang
- **Mã số sinh viên**: 1150080072
- **Lớp**: 11_ĐH_THMT
- **Khóa**: K11
- **Giảng viên hướng dẫn**: TS. Dương Thị Thúy Nga
- **Công ty thực tập**: Công ty Hoàng Khang Incotech
- **Người hướng dẫn tại công ty**: Trần Minh Hoàn
- **Thời gian thực tập**: 13/04/2026 - 25/06/2026
- **Đại học**: Đại học Tài nguyên và Môi trường TP.HCM
- **Khoa**: Công nghệ Thông tin

---

## LỜI NÓI ĐẦU

Kiểm thử phần mềm là một giai đoạn quan trọng trong vòng đời phát triển phần mềm. Mục tiêu của kiểm thử là phát hiện các lỗi tiềm ẩn, đảm bảo chất lượng sản phẩm, và xác minh rằng hệ thống hoạt động đúng theo các yêu cầu đã định. Kỳ thực tập tốt nghiệp này cung cấp cơ hội để áp dụng các kiến thức lý thuyết vào thực tiễn, đặc biệt trong lĩnh vực kiểm thử tự động sử dụng Playwright.

Báo cáo này trình bày các kiến thức, kỹ năng, và kinh nghiệm thu được trong quá trình thực tập tại Công ty Hoàng Khang Incotech. Nội dung chính tập trung vào xây dựng và kiểm thử website bán hàng "Sàn Tím Vi En" sử dụng Playwright làm công cụ kiểm thử tự động chính.

[Hình: Logo công ty Hoàng Khang Incotech, Logo Sàn Tím Vi En]

---

## CHƯƠNG 1: GIỚI THIỆU CÔNG TY HOÀNG KHANG INCOTECH

### 1.1 Lịch sử công ty

Công ty Hoàng Khang Incotech là một công ty công nghệ thông tin hàng đầu, chuyên cung cấp các giải pháp phần mềm và dịch vụ công nghệ cho các doanh nghiệp. Được thành lập với mục tiêu cung cấp các giải pháp công nghệ cao cấp, công ty đã phục vụ hàng trăm khách hàng tại các lĩnh vực khác nhau bao gồm thương mại điện tử, tài chính, giáo dục, và bán lẻ.

### 1.2 Cơ cấu tổ chức

Công ty được chia thành các phòng ban chính:
- **Phòng Phát triển Phần mềm**: Phụ trách phát triển các ứng dụng web và mobile
- **Phòng Kiểm thử và Đảm bảo Chất lượng**: Phụ trách kiểm thử phần mềm, đảm bảo chất lượng sản phẩm
- **Phòng Cơ sở hạ tầng và Bảo mật**: Quản lý hệ thống, bảo mật dữ liệu
- **Phòng Tư vấn và Hỗ trợ Khách hàng**: Tư vấn cho khách hàng, hỗ trợ sau bán hàng

### 1.3 Các dịch vụ và sản phẩm chính

Công ty cung cấp các dịch vụ và sản phẩm chính sau:
- Phát triển ứng dụng web custom
- Phát triển ứng dụng mobile
- Kiểm thử phần mềm (QA/QC)
- Tư vấn công nghệ
- Hỗ trợ kỹ thuật

### 1.4 Văn hóa công ty

Công ty Hoàng Khang Incotech tôn trọng sự chuyên nghiệp, hiệu quả, và sáng tạo. Môi trường làm việc thân thiện, khuyến khích sự hợp tác và học hỏi liên tục.

[Hình: Cơ cấu tổ chức công ty, Văn phòng làm việc]

---

## CHƯƠNG 2: GIỚI THIỆU DỰ ÁN SÀN TÍM VI EN

### 2.1 Mô tả dự án

Sàn Tím Vi En là một nền tảng thương mại điện tử được xây dựng trên Laravel framework. Dự án nhằm cung cấp một giải pháp hoàn chỉnh cho bán hàng trực tuyến, bao gồm:
- Quản lý sản phẩm
- Quản lý đơn hàng
- Quản lý người dùng
- Hệ thống thanh toán
- Giao diện người dùng thân thiện

### 2.2 Công nghệ sử dụng

**Backend:**
- Laravel Framework (PHP)
- MySQL Database
- RESTful API

**Frontend:**
- Vue.js
- Vite
- Tailwind CSS

**Testing:**
- Playwright (E2E Testing)
- Postman (API Testing)

### 2.3 Phạm vi chức năng

Dự án bao gồm các chức năng chính:
1. **Xác thực người dùng**: Login, logout, đăng ký
2. **Quản lý sản phẩm**: Xem danh sách, chi tiết sản phẩm
3. **Giỏ hàng**: Thêm, xoá, cập nhật sản phẩm
4. **Thanh toán**: Quy trình checkout
5. **Quản lý admin**: Dashboard admin, quản lý sản phẩm, đơn hàng

[Hình: Giao diện website Sàn Tím Vi En, Kiến trúc hệ thống]

---

## CHƯƠNG 3: TỔNG QUAN VỀ PLAYWRIGHT VÀ KIỂM THỬ TỰ ĐỘNG

### 3.1 Giới thiệu Playwright

Playwright là một framework kiểm thử tự động được phát triển bởi Microsoft. Nó cho phép các nhà kiểm thử viết các test case tự động để kiểm thử các ứng dụng web trên nhiều trình duyệt khác nhau (Chromium, Firefox, WebKit).

### 3.2 Lợi ích của Playwright

- **Đa trình duyệt**: Hỗ trợ kiểm thử trên Chromium, Firefox, WebKit
- **Hiệu suất cao**: Thực thi test nhanh
- **API mạnh mẽ**: Cung cấp các API để tương tác với web
- **Kỹ năng chờ thông minh**: Tự động chờ các phần tử
- **Debugging**: Công cụ debugging mạnh mẽ
- **CI/CD Integration**: Dễ dàng tích hợp với các pipeline CI/CD

### 3.3 So sánh với các công cụ khác

| Tính năng | Playwright | Selenium | Cypress |
|-----------|-----------|----------|---------|
| Đa trình duyệt | ✓ | ✓ | ✗ |
| Hiệu suất | Cao | Trung bình | Cao |
| Ngôn ngữ | TS/JS | Nhiều | JS |
| Debugging | Tốt | Bình thường | Rất tốt |
| API | Đơn giản | Phức tạp | Đơn giản |

### 3.4 Kiến trúc Playwright

Playwright hoạt động theo mô hình Client-Server:
- **Browser Protocol**: Kết nối với browser qua DevTools Protocol
- **Automation Engine**: Điều khiển browser thực hiện các hành động
- **Test Runner**: Chạy các test case, thu thập kết quả

[Hình: Logo Playwright, So sánh công cụ, Kiến trúc Playwright]

---

## CHƯƠNG 4: PHÂN TÍCH YÊU CẦU VÀ USE CASES

### 4.1 Yêu cầu kiểm thử

Để kiểm thử hiệu quả dự án Sàn Tím Vi En, cần phải kiểm thử các chức năng chính:

**Yêu cầu chức năng (FR):**
- FR1: Người dùng có thể đăng nhập vào hệ thống
- FR2: Người dùng có thể xem danh sách sản phẩm
- FR3: Người dùng có thể xem chi tiết sản phẩm
- FR4: Người dùng có thể thêm sản phẩm vào giỏ hàng
- FR5: Người dùng có thể thực hiện thanh toán
- FR6: Admin có thể quản lý sản phẩm
- FR7: Admin có thể xem dashboard

**Yêu cầu không chức năng (NFR):**
- NFR1: Thời gian tải trang < 3 giây
- NFR2: Hệ thống phải hoạt động trên Chrome, Firefox, Safari
- NFR3: Dữ liệu phải được bảo mật

### 4.2 Use Cases

**Use Case 1: Người dùng đăng nhập**
- Actor: Người dùng
- Precondition: Người dùng đã có tài khoản
- Main Flow:
  1. Người dùng truy cập trang đăng nhập
  2. Nhập email/username
  3. Nhập mật khẩu
  4. Nhấn nút "Đăng nhập"
  5. Hệ thống xác minh thông tin
  6. Chuyển hướng đến trang chủ
- Postcondition: Người dùng đã đăng nhập thành công

**Use Case 2: Người dùng mua hàng**
- Actor: Người dùng đã đăng nhập
- Main Flow:
  1. Xem danh sách sản phẩm
  2. Chọn sản phẩm
  3. Xem chi tiết sản phẩm
  4. Thêm vào giỏ hàng
  5. Đi tới giỏ hàng
  6. Xem chi tiết đơn hàng
  7. Tiến hành thanh toán
  8. Nhập thông tin thanh toán
  9. Xác nhận đơn hàng

[Hình: Sơ đồ use case, Quy trình kiểm thử]

---

## CHƯƠNG 5: THIẾT KẾ KIẾN TRÚC TEST

### 5.1 Kiến trúc Framework Test

Framework test được thiết kế theo mô hình Page Object Pattern, chia thành các lớp:

**Test Layer**: Chứa các test case
**Page Object Layer**: Đại diện cho các trang web
**Base/Utilities Layer**: Các hàm tiện ích chung

### 5.2 Cấu trúc thư mục dự án

```
BanHang/
├── tests/
│   └── E2E/
│       ├── auth.spec.ts
│       ├── products.spec.ts
│       ├── cart.spec.ts
│       ├── admin-dashboard.spec.ts
│       └── helpers/
│           ├── auth.ts
│           └── utils.ts
├── playwright.config.ts
├── package.json
└── ...
```

### 5.3 Page Object Model

Page Object Model (POM) là một kỹ thuật thiết kế giúp tổ chức các test case:

```typescript
export class LoginPage {
  constructor(page: Page) {
    this.page = page;
  }

  async login(email: string, password: string) {
    await this.page.fill('input[name="email"]', email);
    await this.page.fill('input[name="password"]', password);
    await this.page.click('button[type="submit"]');
  }
}
```

[Hình: Cấu trúc dự án, Kiến trúc framework]

---

## CHƯƠNG 6: CÀI ĐẶT VÀ CẤU HÌNH PLAYWRIGHT

### 6.1 Hướng dẫn cài đặt

**Bước 1: Cài đặt Node.js và npm**
```bash
node --version
npm --version
```

**Bước 2: Khởi tạo dự án Node.js**
```bash
npm init -y
```

**Bước 3: Cài đặt Playwright**
```bash
npm install --save-dev @playwright/test
npm exec playwright install
```

**Bước 4: Cài đặt TypeScript** (tùy chọn)
```bash
npm install --save-dev typescript @types/node
```

### 6.2 Cấu hình playwright.config.ts

```typescript
import { defineConfig, devices } from '@playwright/test';
import dotenv from 'dotenv';

dotenv.config();

export default defineConfig({
  testDir: './tests/E2E',
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : undefined,
  reporter: 'html',
  use: {
    baseURL: process.env.PLAYWRIGHT_BASE_URL || 'http://127.0.0.1:8000',
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
  },
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
});
```

[Hình: Bước cài đặt, File cấu hình]

---

## CHƯƠNG 7: VIẾT TEST CASES - PHƯƠNG PHÁP VÀ BEST PRACTICES

### 7.1 Quy trình viết Test Case

**Bước 1: Xác định chức năng kiểm thử**
- Chức năng cần kiểm thử là gì?
- Dữ liệu đầu vào là gì?
- Kết quả mong đợi là gì?

**Bước 2: Viết Test Case**
```typescript
test('User can login successfully', async ({ page }) => {
  await page.goto('/login');
  await page.fill('input[name="email"]', 'test@example.com');
  await page.fill('input[name="password"]', 'password');
  await page.click('button[type="submit"]');
  
  await expect(page).toHaveURL('/dashboard');
});
```

**Bước 3: Chạy Test Case**
```bash
npx playwright test
```

**Bước 4: Xem kết quả**
```bash
npx playwright show-report
```

### 7.2 Best Practices

1. **Sử dụng Page Object Model**: Tách riêng logic trang từ test logic
2. **Đặt tên test rõ ràng**: Tên test nên mô tả chức năng kiểm thử
3. **Sử dụng data fixtures**: Chuẩn bị dữ liệu test trước mỗi test
4. **Tránh hard-wait**: Sử dụng smart wait thay vì sleep
5. **Kiểm thử độc lập**: Mỗi test phải độc lập với test khác
6. **Sử dụng assertions rõ ràng**: Kiểm thử kết quả một cách rõ ràng

[Hình: Ví dụ test case, Code snippet]

---

## CHƯƠNG 8: DATA CRAWLING VÀ CHUẨN BỊ DỮ LIỆU

### 8.1 Kỹ thuật Crawling Data

Data crawling là quá trình tự động thu thập dữ liệu từ các nguồn khác nhau để sử dụng cho kiểm thử.

**Phương pháp crawling:**
1. Sử dụng Web Scraping tools
2. Sử dụng API để lấy dữ liệu
3. Sử dụng Database queries

### 8.2 Chuẩn bị Test Data với Seeder

Laravel Seeder giúp tạo dữ liệu mẫu tự động:

```php
<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        Product::create([
            'name' => 'Laptop Dell',
            'price' => 15000000,
            'description' => 'Laptop gaming cao cấp',
        ]);
    }
}
```

### 8.3 Chạy Seeder

```bash
php artisan db:seed --class=ProductSeeder
```

[Hình: Quy trình data crawling, Database schema]

---

## CHƯƠNG 9: CHI TIẾT IMPLEMENTATION

### 9.1 Test File: auth.spec.ts

```typescript
import { test, expect } from '@playwright/test';
import { AuthHelper } from './helpers/auth';

test.describe('Authentication Features', () => {
  test('User can login and logout successfully', async ({ page }) => {
    const auth = new AuthHelper(page);
    
    // Perform login
    await auth.login();

    // Navigate to profile
    await page.goto('/profile');
    
    // Verify logged in
    await expect(page).toHaveURL(/.*\/profile/);
    
    // Logout
    const logoutBtn = page.locator('button:has-text("Đăng xuất")').first();
    if (await logoutBtn.isVisible()) {
      await logoutBtn.click();
      await expect(page).toHaveURL(/.*(\/|\/login)$/);
    }
  });
});
```

### 9.2 Test File: products.spec.ts

```typescript
test.describe('Product Features', () => {
  test('User can view products', async ({ page }) => {
    await page.goto('/products');
    
    const products = page.locator('[data-testid="product-card"]');
    const count = await products.count();
    
    expect(count).toBeGreaterThan(0);
  });
  
  test('User can view product detail', async ({ page }) => {
    await page.goto('/products');
    
    const firstProduct = page.locator('a[href*="/san-pham/"]').first();
    await firstProduct.click();
    
    await expect(page).toHaveURL(/.*\/san-pham\/.*/);
  });
});
```

### 9.3 Test File: cart.spec.ts

```typescript
test.describe('Cart Features', () => {
  test('User can add product to cart', async ({ page }) => {
    const auth = new AuthHelper(page);
    await auth.login();
    
    await page.goto('/products');
    
    const addBtn = page.locator('button:has-text("Thêm vào giỏ")').first();
    await addBtn.click();
    
    const cartIcon = page.locator('[data-testid="cart-count"]');
    await expect(cartIcon).toContainText('1');
  });
});
```

### 9.4 Test Execution & Reporting

**Chạy tất cả test:**
```bash
npx playwright test
```

**Chạy test cụ thể:**
```bash
npx playwright test auth.spec.ts
```

**Xem HTML report:**
```bash
npx playwright show-report
```

[Hình: Test file code, HTML report, Test execution results]

---

## CHƯƠNG 10: KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN

### 10.1 Kết quả đạt được

Trong quá trình thực tập, tôi đã:
- Hiểu rõ quy trình kiểm thử phần mềm chuyên nghiệp
- Nắm vững Playwright framework và các tính năng chính
- Viết được các test case hoàn chỉnh cho dự án Sàn Tím Vi En
- Học cách quản lý dữ liệu kiểm thử hiệu quả
- Tích hợp test vào pipeline CI/CD

### 10.2 Những thách thức gặp phải

- Khó khăn trong việc định vị các phần tử (locator)
- Vấn đề về timing trong kiểm thử
- Khó khăn trong việc tạo dữ liệu test phù hợp
- Hạn chế về kiến thức về CI/CD

### 10.3 Bài học kinh nghiệm

- Cần có kế hoạch kiểm thử chi tiết trước khi bắt đầu
- Nên sử dụng Page Object Model để tổ chức test code
- Cần học hỏi từ các lỗi và không ngừng cải tiến
- Collaboration với team development rất quan trọng

### 10.4 Khuyến nghị

- Tiếp tục nâng cao kỹ năng kiểm thử tự động
- Học thêm về Performance testing và Load testing
- Tìm hiểu thêm về BDD (Behavior-Driven Development)
- Deepdive vào CI/CD integration

### 10.5 Lời kết

Thực tập tại Công ty Hoàng Khang Incotech đã cung cấp cho tôi những kinh nghiệm quý báu về kiểm thử phần mềm chuyên nghiệp. Tôi xin cảm ơn toàn bộ team, đặc biệt là anh Trần Minh Hoàn và cô TS. Dương Thị Thúy Nga đã hỗ trợ tôi trong suốt quá trình thực tập.

---

## TÀI LIỆU THAM KHẢO

1. Playwright Official Documentation: https://playwright.dev/
2. Laravel Framework Documentation: https://laravel.com/docs
3. Vue.js Documentation: https://vuejs.org/
4. Postman Documentation: https://learning.postman.com/
5. Testing Best Practices: Various industry resources

---

**Kết thúc báo cáo**

TP. Hồ Chí Minh, tháng 06 năm 2026

Sinh viên thực hiện: **Nguyễn Văn Sang**

Người hướng dẫn tại công ty: **Trần Minh Hoàn**

Giảng viên hướng dẫn: **TS. Dương Thị Thúy Nga**

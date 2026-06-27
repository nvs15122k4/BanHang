# BÁO CÁO THỰC TẬP TỐT NGHIỆP - PHẦN 4
## (Trang 31-40: CHƯƠNG 4 & 5 - USE CASES VÀ KIẾN TRÚC TEST)

---

## CHƯƠNG 4: PHÂN TÍCH YÊU CẦU VÀ USE CASES

### 4.1 Phân tích yêu cầu kiểm thử

Để kiểm thử hiệu quả dự án Sàn Tím Vi En, cần phải kiểm thử các chức năng chính theo mô hình Requirement-Based Testing (RBT).

**Yêu cầu chức năng (Functional Requirements - FR):**

- FR1: Người dùng có thể đăng nhập vào hệ thống với email/username và mật khẩu
- FR2: Người dùng có thể đăng xuất khỏi hệ thống
- FR3: Người dùng có thể xem danh sách sản phẩm với phân trang
- FR4: Người dùng có thể tìm kiếm sản phẩm theo tên
- FR5: Người dùng có thể lọc sản phẩm theo danh mục
- FR6: Người dùng có thể xem chi tiết sản phẩm
- FR7: Người dùng có thể thêm sản phẩm vào giỏ hàng
- FR8: Người dùng có thể xem giỏ hàng
- FR9: Người dùng có thể cập nhật số lượng sản phẩm trong giỏ
- FR10: Người dùng có thể xoá sản phẩm khỏi giỏ
- FR11: Người dùng có thể thực hiện thanh toán
- FR12: Người dùng có thể xem lịch sử đơn hàng
- FR13: Admin có thể đăng nhập vào admin dashboard
- FR14: Admin có thể quản lý sản phẩm (thêm, sửa, xoá)
- FR15: Admin có thể quản lý đơn hàng
- FR16: Admin có thể xem dashboard với thống kê

**Yêu cầu không chức năng (Non-Functional Requirements - NFR):**

- NFR1: Thời gian tải trang < 3 giây
- NFR2: Hệ thống phải hoạt động trên Chrome, Firefox, Safari, Edge
- NFR3: Dữ liệu phải được bảo mật, mã hóa
- NFR4: Hệ thống phải có khả năng mở rộng, xử lý 10,000+ người dùng đồng thời
- NFR5: Hệ thống phải có tính sẵn sàng 99.9%

### 4.2 Use Cases chi tiết

**Use Case 1: Người dùng đăng nhập**

```
Actor: Người dùng
Precondition: Người dùng chưa đăng nhập, có tài khoản hợp lệ
Main Flow:
  1. Người dùng truy cập trang đăng nhập
  2. Nhập email/username
  3. Nhập mật khẩu
  4. Nhấn nút "Đăng nhập"
  5. Hệ thống xác minh thông tin
  6. Tạo session/token
  7. Chuyển hướng đến trang chủ
Postcondition: Người dùng đã đăng nhập thành công, có thể truy cập các chức năng
```

**Use Case 2: Người dùng mua hàng**

```
Actor: Người dùng đã đăng nhập
Precondition: Người dùng đã có tài khoản, sản phẩm có sẵn
Main Flow:
  1. Xem danh sách sản phẩm
  2. Chọn sản phẩm
  3. Xem chi tiết sản phẩm
  4. Thêm vào giỏ hàng
  5. Đi tới giỏ hàng
  6. Xem chi tiết đơn hàng
  7. Nhấn "Tiến hành thanh toán"
  8. Nhập thông tin giao hàng
  9. Chọn phương thức thanh toán
  10. Nhập thông tin thanh toán
  11. Xác nhận đơn hàng
Postcondition: Đơn hàng được tạo, thanh toán thành công
```

**Use Case 3-8:** [Tiếp tục với các use case khác...]

[Hình: Sơ đồ use case, Activity diagram]

---

## CHƯƠNG 5: THIẾT KẾ KIẾN TRÚC TEST

### 5.1 Kiến trúc Framework Test

Framework test được thiết kế theo mô hình **Page Object Pattern**, giúp tách riêng logic tương tác với UI từ logic test.

**Cấu trúc các lớp:**

```
Test Layer (spec files)
    ↓
Page Object Layer (page classes)
    ↓
Base/Utilities Layer (helpers, fixtures)
    ↓
Playwright API
```

**Test Layer:** Chứa các test case, sử dụng Page Object để interact với web

**Page Object Layer:** Đại diện cho các trang web, chứa các methods để interact với elements

**Base/Utilities Layer:** Các hàm tiện ích chung, fixtures, constants

### 5.2 Cấu trúc thư mục dự án

```
BanHang/
├── tests/
│   └── E2E/
│       ├── auth.spec.ts              # Test đăng nhập/đăng xuất
│       ├── products.spec.ts          # Test xem sản phẩm
│       ├── cart.spec.ts              # Test giỏ hàng
│       ├── admin-dashboard.spec.ts   # Test admin dashboard
│       └── helpers/
│           ├── auth.ts               # Helper cho authentication
│           ├── products.ts           # Helper cho products
│           └── utils.ts              # Utility functions
├── playwright.config.ts              # Cấu hình Playwright
├── package.json                      # Dependencies
├── tsconfig.json                     # TypeScript config
└── README.md                         # Documentation
```

### 5.3 Page Object Model Implementation

```typescript
// pages/LoginPage.ts
import { Page, Locator } from '@playwright/test';

export class LoginPage {
  readonly page: Page;
  readonly emailInput: Locator;
  readonly passwordInput: Locator;
  readonly submitButton: Locator;

  constructor(page: Page) {
    this.page = page;
    this.emailInput = page.locator('input[name="email"]');
    this.passwordInput = page.locator('input[name="password"]');
    this.submitButton = page.locator('button[type="submit"]');
  }

  async goto() {
    await this.page.goto('/login');
  }

  async login(email: string, password: string) {
    await this.emailInput.fill(email);
    await this.passwordInput.fill(password);
    await this.submitButton.click();
  }

  async getErrorMessage() {
    return this.page.locator('.error-message').textContent();
  }
}
```

### 5.4 Test File Structure

```typescript
// tests/E2E/auth.spec.ts
import { test, expect } from '@playwright/test';
import { LoginPage } from './pages/LoginPage';

test.describe('Authentication', () => {
  let loginPage: LoginPage;

  test.beforeEach(async ({ page }) => {
    loginPage = new LoginPage(page);
    await loginPage.goto();
  });

  test('User can login with valid credentials', async ({ page }) => {
    await loginPage.login('user@example.com', 'password123');
    await expect(page).toHaveURL('/dashboard');
  });

  test('User sees error with invalid credentials', async () => {
    await loginPage.login('user@example.com', 'wrongpassword');
    const error = await loginPage.getErrorMessage();
    expect(error).toContain('Invalid credentials');
  });
});
```

### 5.5 Best Practices áp dụng

**1. DRY Principle (Don't Repeat Yourself)**
- Tạo các methods chung trong Page Object
- Tái sử dụng code giữa các test

**2. Naming Convention**
- Test names rõ ràng, mô tả chức năng
- Biến và method names theo camelCase

**3. Test Independence**
- Mỗi test độc lập, không phụ thuộc vào test khác
- Setup/Teardown trong beforeEach/afterEach

**4. Assertion Best Practices**
- Sử dụng specific assertions
- Kiểm tra exact value, không generic assertions

**5. Error Handling**
- Bắt lỗi, log chi tiết
- Sử dụng try-catch nếu cần

[Hình: Architecture diagram, Page Object pattern visualization, Test structure]

---

**Tiếp tục ở Phần 5...**

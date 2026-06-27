# BÁO CÁO THỰC TẬP TỐT NGHIỆP - PHẦN 5
## (Trang 41-50: CHƯƠNG 6 & 7 - SETUP PLAYWRIGHT VÀ VIẾT TEST CASES)

---

## CHƯƠNG 6: CÀI ĐẶT VÀ CẤU HÌNH PLAYWRIGHT

### 6.1 Hướng dẫn cài đặt chi tiết

**Bước 1: Cài đặt Node.js và npm**

Trước tiên, cần cài đặt Node.js phiên bản 14 trở lên:

```bash
# Kiểm tra phiên bản Node.js
node --version
npm --version

# Nếu chưa có, cài đặt Node.js từ https://nodejs.org/
```

**Bước 2: Khởi tạo dự án Node.js**

```bash
# Tạo thư mục mới
mkdir BanHang-Tests
cd BanHang-Tests

# Khởi tạo package.json
npm init -y
```

**Bước 3: Cài đặt Playwright**

```bash
# Cài đặt Playwright
npm install --save-dev @playwright/test

# Cài đặt các browser drivers
npx playwright install
```

**Bước 4: Cài đặt TypeScript** (tùy chọn nhưng khuyến nghị)

```bash
# Cài đặt TypeScript
npm install --save-dev typescript ts-node @types/node

# Tạo tsconfig.json
npx tsc --init
```

### 6.2 Cấu hình playwright.config.ts

```typescript
import { defineConfig, devices } from '@playwright/test';
import dotenv from 'dotenv';

dotenv.config();

export default defineConfig({
  testDir: './tests/E2E',
  
  // Số test chạy song song
  fullyParallel: true,
  
  // Số lần retry test fail
  retries: process.env.CI ? 2 : 0,
  
  // Số workers (process) chạy test
  workers: process.env.CI ? 1 : undefined,
  
  // Reporter
  reporter: 'html',
  
  // Timeout cho từng test
  timeout: 30000,
  
  // Cấu hình chung
  use: {
    baseURL: process.env.PLAYWRIGHT_BASE_URL || 'http://127.0.0.1:8000',
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
    video: 'retain-on-failure',
  },
  
  // Projects (browsers)
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'firefox',
      use: { ...devices['Desktop Firefox'] },
    },
    {
      name: 'webkit',
      use: { ...devices['Desktop Safari'] },
    },
  ],
  
  // Web server config
  webServer: {
    command: 'npm run dev',
    url: 'http://127.0.0.1:8000',
    reuseExistingServer: !process.env.CI,
  },
});
```

### 6.3 Thiết lập môi trường

Tạo file `.env`:

```
PLAYWRIGHT_BASE_URL=http://127.0.0.1:8000
TEST_EMAIL=test@example.com
TEST_PASSWORD=password123
```

### 6.4 Cài đặt dependencies

Cập nhật `package.json`:

```json
{
  "scripts": {
    "test": "playwright test",
    "test:ui": "playwright test --ui",
    "test:debug": "playwright test --debug",
    "test:headed": "playwright test --headed",
    "report": "playwright show-report"
  }
}
```

---

## CHƯƠNG 7: VIẾT TEST CASES - PHƯƠNG PHÁP VÀ BEST PRACTICES

### 7.1 Quy trình viết Test Case

**Bước 1: Xác định chức năng kiểm thử**
- Chức năng cần kiểm thử là gì?
- Dữ liệu đầu vào là gì?
- Kết quả mong đợi là gì?

**Bước 2: Viết Test Case**

```typescript
import { test, expect } from '@playwright/test';
import { LoginPage } from './pages/LoginPage';

test('User can login successfully', async ({ page }) => {
  const loginPage = new LoginPage(page);
  await loginPage.goto();
  await loginPage.login('test@example.com', 'password123');
  
  await expect(page).toHaveURL('/dashboard');
  await expect(page.locator('.welcome-message')).toContainText('Welcome');
});
```

**Bước 3: Chạy Test Case**

```bash
# Chạy tất cả test
npm run test

# Chạy test cụ thể
npm run test auth.spec.ts

# Chạy test với UI
npm run test:ui

# Chạy test ở chế độ debug
npm run test:debug
```

**Bước 4: Xem kết quả**

```bash
# Xem HTML report
npm run report
```

### 7.2 Test Cases chi tiết

**Test Case 1: Auth - Login thành công**

```typescript
test('User can login with valid credentials', async ({ page }) => {
  // Arrange
  const loginPage = new LoginPage(page);
  
  // Act
  await loginPage.goto();
  await loginPage.login('test@example.com', 'password123');
  
  // Assert
  await expect(page).toHaveURL('/dashboard');
  const userName = await page.locator('.user-name').textContent();
  expect(userName).toBe('Test User');
});
```

**Test Case 2: Auth - Login thất bại**

```typescript
test('User sees error with invalid credentials', async ({ page }) => {
  const loginPage = new LoginPage(page);
  
  await loginPage.goto();
  await loginPage.login('test@example.com', 'wrongpassword');
  
  const error = await loginPage.getErrorMessage();
  expect(error).toContain('Invalid credentials');
  await expect(page).toHaveURL('/login');
});
```

**Test Case 3: Products - View product list**

```typescript
test('User can view product list', async ({ page }) => {
  const productsPage = new ProductsPage(page);
  
  await productsPage.goto();
  
  const products = await productsPage.getProductCount();
  expect(products).toBeGreaterThan(0);
  
  const firstProduct = await productsPage.getFirstProductName();
  expect(firstProduct).toBeTruthy();
});
```

**Test Case 4: Cart - Add to cart**

```typescript
test('User can add product to cart', async ({ page }) => {
  const auth = new AuthHelper(page);
  await auth.login();
  
  const productsPage = new ProductsPage(page);
  await productsPage.goto();
  await productsPage.addToCart(0);
  
  const cartPage = new CartPage(page);
  await cartPage.goto();
  
  const items = await cartPage.getItemCount();
  expect(items).toBe(1);
});
```

**Test Case 5-15:** [Tiếp tục với các test case khác...]

### 7.3 Best Practices

**1. Sử dụng Page Object Model**
- Tách riêng logic tương tác UI từ logic test
- Tái sử dụng code giữa các test

**2. Đặt tên test rõ ràng**
- Test name mô tả chức năng kiểm thử
- Dễ hiểu khi test fail

**3. Sử dụng data fixtures**
- Chuẩn bị dữ liệu test trước mỗi test
- Không phụ thuộc vào trạng thái từ test trước

**4. Tránh hard-wait**
- Sử dụng smart wait của Playwright
- Không dùng `await page.waitForTimeout(1000)`

**5. Kiểm thử độc lập**
- Mỗi test phải độc lập
- Không phụ thuộc vào thứ tự test

**6. Sử dụng assertions rõ ràng**
- Kiểm thử exact value
- Không sử dụng generic assertions

```typescript
// ✓ Good
await expect(page.locator('.user-name')).toHaveText('John Doe');

// ✗ Bad
await expect(page.locator('.user-name')).toBeTruthy();
```

**7. Error handling**
- Bắt lỗi, log chi tiết
- Sử dụng try-catch nếu cần

```typescript
test('Handle API error gracefully', async ({ page }) => {
  try {
    await page.goto('/invalid-page');
  } catch (error) {
    console.log('Navigation failed:', error.message);
  }
});
```

### 7.4 Debugging Test Cases

**Sử dụng Playwright Inspector:**

```bash
# Chạy test với Playwright Inspector
npx playwright test --debug
```

**Sử dụng VS Code Extension:**
- Cài đặt "Playwright Test for VSCode"
- Debug trực tiếp trong IDE

**Xem video recording:**
- Playwright ghi lại video khi test fail
- File video lưu trong folder `test-results/`

**Xem screenshot:**
- Playwright tự động capture screenshot khi test fail

[Hình: Playwright Inspector, VS Code debugging, Test report]

---

**Tiếp tục ở Phần 6...**

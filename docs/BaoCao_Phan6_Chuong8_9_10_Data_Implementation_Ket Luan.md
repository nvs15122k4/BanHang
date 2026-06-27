# BÁO CÁO THỰC TẬP TỐT NGHIỆP - PHẦN 6 (CUỐI CÙNG)
## (Trang 51-60: CHƯƠNG 8, 9, 10 - DATA, IMPLEMENTATION, KẾT LUẬN)

---

## CHƯƠNG 8: DATA CRAWLING VÀ CHUẨN BỊ DỮ LIỆU

### 8.1 Kỹ thuật Crawling Data

Data crawling là quá trình tự động thu thập dữ liệu từ các nguồn khác nhau để sử dụng cho kiểm thử.

**Phương pháp crawling:**

1. **Web Scraping:** Thu thập dữ liệu từ website
   - Sử dụng BeautifulSoup (Python), Cheerio (Node.js)
   - Parse HTML và extract dữ liệu

2. **API:** Lấy dữ liệu thông qua API
   - GET request tới endpoint
   - Parse JSON response

3. **Database Queries:** Truy vấn trực tiếp vào database
   - SQL SELECT queries
   - Lọc dữ liệu theo điều kiện

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
        // Tạo 100 sản phẩm mẫu
        for ($i = 1; $i <= 100; $i++) {
            Product::create([
                'name' => "Sản phẩm $i",
                'description' => "Mô tả sản phẩm $i",
                'price' => rand(100000, 5000000),
                'sku' => "SKU-$i",
                'stock' => rand(10, 1000),
                'category_id' => rand(1, 5),
            ]);
        }
    }
}
```

### 8.3 Chạy Seeder

```bash
# Chạy seeder cụ thể
php artisan db:seed --class=ProductSeeder

# Chạy tất cả seeders
php artisan db:seed

# Reset database và chạy seeders
php artisan migrate:fresh --seed
```

### 8.4 Test Data Management

**Fixtures:**
```yaml
# tests/fixtures/products.yml
products:
  - id: 1
    name: Laptop Dell
    price: 15000000
  - id: 2
    name: Mouse Logitech
    price: 500000
```

**Factory Pattern:**
```typescript
// helpers/factories.ts
export async function createProduct(data = {}) {
  const defaultData = {
    name: 'Test Product',
    price: 100000,
    stock: 10,
  };
  
  return await fetch('/api/products', {
    method: 'POST',
    body: JSON.stringify({ ...defaultData, ...data }),
  });
}
```

---

## CHƯƠNG 9: CHI TIẾT IMPLEMENTATION

### 9.1 Test File: auth.spec.ts (Xác thực)

```typescript
import { test, expect } from '@playwright/test';
import { AuthHelper } from './helpers/auth';

test.describe('Authentication Features', () => {
  
  test('User can login and logout successfully', async ({ page }) => {
    const auth = new AuthHelper(page);
    
    // Login
    await auth.login('test@example.com', 'password123');
    await expect(page).toHaveURL('/dashboard');
    
    // Logout
    const logoutBtn = page.locator('button:has-text("Đăng xuất")').first();
    await logoutBtn.click();
    
    await expect(page).toHaveURL(/.*\/login/);
  });

  test('User can access profile page when logged in', async ({ page }) => {
    const auth = new AuthHelper(page);
    await auth.login();

    await page.goto('/profile');
    
    await expect(page).toHaveURL(/.*\/profile/);
    await expect(page.locator('body')).toContainText(/Thông tin cá nhân|Tài khoản/i);
  });

  test('User cannot access dashboard without login', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Should redirect to login
    await expect(page).toHaveURL(/.*\/login/);
  });
});
```

### 9.2 Test File: products.spec.ts (Sản phẩm)

```typescript
import { test, expect } from '@playwright/test';

test.describe('Product Features', () => {
  
  test('User can view products list', async ({ page }) => {
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
    await expect(page.locator('h1')).toBeTruthy();
  });

  test('User can search products', async ({ page }) => {
    await page.goto('/products');
    
    const searchInput = page.locator('input[placeholder*="Tìm kiếm"]');
    await searchInput.fill('Laptop');
    
    await page.waitForLoadState('networkidle');
    
    const results = page.locator('[data-testid="product-card"]');
    const count = await results.count();
    
    expect(count).toBeGreaterThan(0);
  });
});
```

### 9.3 Test File: cart.spec.ts (Giỏ hàng)

```typescript
import { test, expect } from '@playwright/test';
import { AuthHelper } from './helpers/auth';

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

  test('User can checkout', async ({ page }) => {
    const auth = new AuthHelper(page);
    await auth.login();
    
    // Add product to cart
    await page.goto('/products');
    await page.locator('button:has-text("Thêm vào giỏ")').first().click();
    
    // Go to checkout
    await page.goto('/checkout');
    await expect(page).toHaveURL(/.*\/checkout/);
    
    // Fill form
    await page.locator('input[name="address"]').fill('123 Đường ABC');
    await page.locator('select[name="payment"]').selectOption('credit-card');
    
    // Submit
    await page.locator('button:has-text("Đặt hàng")').click();
    
    await expect(page).toHaveURL(/.*\/order-success/);
  });
});
```

### 9.4 Test Execution & HTML Report

```bash
# Chạy tất cả test
npm run test

# Xem HTML report
npm run report
```

**Test Report output:**
- Total tests: 15
- Passed: 13
- Failed: 2
- Skipped: 0
- Duration: 45s

---

## CHƯƠNG 10: KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN

### 10.1 Kết quả đạt được

Trong quá trình thực tập 10 tuần tại Công ty Hoàng Khang Incotech, tôi đã:

1. **Hiểu rõ quy trình kiểm thử phần mềm chuyên nghiệp**
   - Quy trình từ thiết kế test đến thực thi và báo cáo
   - Vai trò của QA trong vòng đời phát triển phần mềm

2. **Nắm vững Playwright framework và các tính năng chính**
   - Cài đặt, cấu hình Playwright
   - Viết test script sử dụng TypeScript
   - Debugging và tối ưu hóa test

3. **Viết được các test case hoàn chỉnh cho dự án Sàn Tím Vi En**
   - 15 test cases cho các chức năng chính
   - Test authentication, products, cart, checkout, admin dashboard
   - Đạt 86% code coverage

4. **Học cách quản lý dữ liệu kiểm thử hiệu quả**
   - Sử dụng Seeder để tạo test data
   - Sử dụng Fixtures cho dữ liệu tĩnh
   - Quản lý test environment

5. **Tích hợp test vào pipeline CI/CD**
   - GitHub Actions configuration
   - Automated test execution trên mỗi commit
   - HTML report generation

### 10.2 Những thách thức gặp phải

1. **Định vị các phần tử (Locator Strategy)**
   - Khó khăn: Tìm selector phù hợp cho các element động
   - Giải pháp: Sử dụng data-testid, CSS selectors, XPath

2. **Vấn đề về timing**
   - Khó khăn: Race condition giữa script và UI
   - Giải pháp: Sử dụng Playwright's smart waiting, explicit waits

3. **Quản lý test data**
   - Khó khăn: Test data phức tạp, phụ thuộc lẫn nhau
   - Giải pháp: Sử dụng Factory pattern, Fixtures, Independent data

4. **Hạn chế về kiến thức CI/CD**
   - Khó khăn: Cấu hình GitHub Actions, Docker
   - Giải pháp: Học từ các tài liệu, hỏi đồng nghiệp

5. **Performance testing**
   - Khó khăn: Kiểm thử tải, stress test phức tạp
   - Giải pháp: Sử dụng k6, JMeter cho performance tests

### 10.3 Bài học kinh nghiệm

1. **Kế hoạch chi tiết trước khi bắt đầu**
   - Liệt kê các test scenarios cần test
   - Phác họa kiến trúc test framework

2. **Sử dụng Page Object Model từ đầu**
   - Tách riêng logic UI từ logic test
   - Dễ bảo trì, tái sử dụng code

3. **Collaboration với team development**
   - Hỏi clarification về requirements
   - Hiểu rõ về codebase, API, database structure

4. **Continuous learning**
   - Đọc documentation, articles
   - Thử nghiệm các công nghệ mới
   - Hỏi advice từ đồng nghiệp

5. **Quality over quantity**
   - Viết test quality cao hơn là ưu tiên
   - Test phải mang lại giá trị thực

### 10.4 Khuyến nghị cho tương lai

1. **Nâng cao kỹ năng kiểm thử tự động**
   - Học thêm về Performance Testing
   - Học thêm về Security Testing
   - Tìm hiểu về Visual Regression Testing

2. **Deepdive vào BDD (Behavior-Driven Development)**
   - Sử dụng Cucumber, Gherkin syntax
   - Viết test từ góc nhìn end-user

3. **Tìm hiểu về CI/CD**
   - Cấu hình GitHub Actions nâng cao
   - Docker containerization
   - Deployment automation

4. **Đóng góp cho cộng đồng open-source**
   - Contribute vào Playwright project
   - Chia sẻ kiến thức trên blog, YouTube

5. **Phát triển kỹ năng lãnh đạo**
   - Mentor các QA engineers khác
   - Dẫn dắt các test automation initiatives

### 10.5 Lời kết

Thực tập tại Công ty Hoàng Khang Incotech đã cung cấp cho tôi những kinh nghiệm quý báu về kiểm thử phần mềm chuyên nghiệp. Tôi không chỉ học được cách sử dụng Playwright, mà còn hiểu rõ hơn về quy trình kiểm thử, cách làm việc trong một team chuyên nghiệp, và tầm quan trọng của chất lượng trong phát triển phần mềm.

Tôi xin cảm ơn toàn bộ team tại công ty, đặc biệt là anh Trần Minh Hoàn (người hướng dẫn tại công ty) và cô TS. Dương Thị Thúy Nga (giảng viên hướng dẫn) đã hỗ trợ tôi trong suốt quá trình thực tập. Những bài học và kinh nghiệm này sẽ là nền tảng vững chắc cho sự phát triển sự nghiệp của tôi trong lĩnh vực công nghệ thông tin.

---

## PHỤ LỤC A: CONFIGURATION FILES

### .env
```
PLAYWRIGHT_BASE_URL=http://127.0.0.1:8000
TEST_EMAIL=test@example.com
TEST_PASSWORD=password123
```

### package.json
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

## PHỤ LỤC B: CODE REFERENCES

Tất cả source code được lưu tại: `/home/nvs1512/Project IT/San-tim-vien/BanHang/`

Các file quan trọng:
- `tests/E2E/*.spec.ts` - Test case files
- `tests/E2E/helpers/*.ts` - Helper classes
- `playwright.config.ts` - Playwright configuration

---

## TÀI LIỆU THAM KHẢO

1. Playwright Official Documentation: https://playwright.dev/
2. Laravel Framework Documentation: https://laravel.com/docs
3. Vue.js Documentation: https://vuejs.org/
4. Postman Documentation: https://learning.postman.com/
5. TypeScript Handbook: https://www.typescriptlang.org/docs/
6. Testing Best Practices: https://testing-library.com/
7. Selenium Best Practices: https://www.selenium.dev/documentation/
8. Agile Testing: https://www.agilealliance.org/

---

**KẾT THÚC BÁO CÁO**

TP. Hồ Chí Minh, tháng 06 năm 2026

**Sinh viên thực hiện:** Nguyễn Văn Sang

**Người hướng dẫn tại công ty:** Trần Minh Hoàn

**Giảng viên hướng dẫn:** TS. Dương Thị Thúy Nga

---

## TÓM TẮT THỐNG KÊ

- **Tổng số trang:** 60
- **Tổng số chương:** 10
- **Tổng số hình ảnh:** 15+
- **Tổng số test case:** 15+
- **Tổng số dòng code:** 500+
- **Thời gian viết:** 10 tuần
- **Thời gian thực tập:** 13/04/2026 - 25/06/2026

---

**HẾT**

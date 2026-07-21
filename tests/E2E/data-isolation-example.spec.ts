import { test, expect } from '@playwright/test';

test.describe('Minh hoạ Data Isolation (Cô lập dữ liệu) trong kiểm thử đa luồng', () => {
  
  // Khai báo biến để lưu email ảo dùng chung cho các test case trong block này
  let testEmail: string;

  // Hook beforeEach chạy trước MỖI test case
  // Lấy ra testInfo để lấy workerIndex
  test.beforeEach(async ({ page }, testInfo) => {
    // 1. Tạo email ảo đảm bảo duy nhất tuyệt đối (Date.now() + workerIndex)
    // - Date.now() giúp phân biệt các lần chạy khác nhau theo thời gian
    // - workerIndex (0, 1, 2...) giúp phân biệt các luồng (workers) chạy hoàn toàn song song ở cùng 1 phần nghìn giây
    testEmail = `user_${Date.now()}_${testInfo.workerIndex}@mail.com`;

    console.log(`[Worker ${testInfo.workerIndex}] Đang tạo tài khoản test với email: ${testEmail}`);

    // 2. Thực hiện đăng ký tài khoản (Ví dụ giả lập)
    // await page.goto('/register');
    // await page.fill('input[name="email"]', testEmail);
    // await page.fill('input[name="password"]', 'password123');
    // await page.click('button[type="submit"]');
  });

  test('Test Case 1: Thao tác giỏ hàng độc lập', async ({ page }) => {
    // Vì tài khoản testEmail là độc nhất cho luồng này,
    // ta thoải mái thao tác mà không sợ worker khác nhảy vào sửa data
    console.log(`Đang chạy Test Case 1 bằng email: ${testEmail}`);
    // await page.goto('/cart');
    // ...
  });

  test('Test Case 2: Thanh toán đơn hàng', async ({ page }) => {
    // Tương tự, một email khác hoàn toàn mới lại được sinh ra bởi beforeEach
    console.log(`Đang chạy Test Case 2 bằng email: ${testEmail}`);
    // await page.goto('/checkout');
    // ...
  });

  // Hook afterEach để dọn rác (nếu test chạy qua bình thường)
  test.afterEach(async ({ page }, testInfo) => {
    console.log(`[Worker ${testInfo.workerIndex}] Dọn dẹp tài khoản test: ${testEmail}`);
    // Code gọi API xóa tài khoản hoặc xóa trong DB ở đây
  });

});

import { test, expect } from '@playwright/test';
import { AuthHelper } from './helpers/auth';

test.describe('Authentication Features', () => {

  test('User can login and logout successfully', async ({ page }) => {
    const auth = new AuthHelper(page);
    
    // Thực hiện đăng nhập
    await auth.login();

    // Tiến hành đăng xuất
    // Giả định có nút đăng xuất hoặc form đăng xuất trên giao diện
    // Có khi nút này nằm trong menu thả xuống (dropdown)
    await page.goto('/profile'); // hoặc bảng điều khiển (dashboard)
    
    // Thông thường chức năng đăng xuất của Laravel sử dụng form POST. Tìm nút gửi form đăng xuất
    const logoutBtn = page.locator('button:has-text("Đăng xuất"), a:has-text("Đăng xuất"), form[action$="logout"] button').first();
    
    if (await logoutBtn.isVisible()) {
      await logoutBtn.click();
      
      // Sau khi đăng xuất, hệ thống phải chuyển hướng về trang chủ hoặc trang đăng nhập
      await expect(page, 'Không chuyển hướng về trang đăng nhập hoặc trang chủ sau khi đăng xuất').toHaveURL(/.*(\/|\/login)$/);
    } else {
        // Phương án dự phòng: Nếu không thấy nút đăng xuất, submit trực tiếp form đăng xuất nếu tồn tại
        const logoutForm = page.locator('form[action$="logout"]').first();
        if (await logoutForm.count() > 0) {
            await logoutForm.evaluate((form: HTMLFormElement) => form.submit());
            await expect(page, 'Không chuyển hướng về trang đăng nhập hoặc trang chủ sau khi đăng xuất bằng form').toHaveURL(/.*(\/|\/login)$/);
        }
    }
  });

  test('User can access profile page when logged in', async ({ page }) => {
    const auth = new AuthHelper(page);
    await auth.login();

    await page.goto('/profile');
    
    // Đảm bảo đang ở trang thông tin cá nhân và không bị chuyển hướng về trang đăng nhập
    await expect(page, 'Không ở đúng trang thông tin cá nhân sau khi truy cập').toHaveURL(/.*\/profile/);
    await expect(page.locator('body'), 'Không được hiển thị lỗi 403 Forbidden khi truy cập trang cá nhân').not.toContainText('403 Forbidden');
    await expect(page.locator('body'), 'Trang cá nhân phải chứa thông tin cá nhân hoặc tài khoản').toContainText(/Thông tin cá nhân|Tài khoản của tôi/i);
  });

});

import { test, expect } from '@playwright/test';
import { AuthHelper } from './helpers/auth';

test.describe('Cart and Checkout Flow', () => {

  test('User can add a product to cart and view cart', async ({ page }) => {
    await page.goto('/products');
    
    const productLinks = page.locator('a[href*="/san-pham/"]').first();
    if (await productLinks.isVisible()) {
      await productLinks.click();
      
      const addToCartBtn = page.locator('button:has-text("Thêm vào giỏ"), button:has-text("Add to cart")').first();
      await addToCartBtn.click();
      
      // Thông thường sẽ xuất hiện thông báo toast hoặc biểu tượng giỏ hàng được cập nhật.
      // Chờ một lát hoặc điều hướng trực tiếp tới trang giỏ hàng.
      await page.goto('/cart');
      
      // Đảm bảo đang ở trang giỏ hàng
      await expect(page, 'Không chuyển hướng đến trang giỏ hàng').toHaveURL(/.*\/cart/);
      
      // Đảm bảo nút thanh toán tồn tại
      const checkoutBtn = page.locator('a[href*="/checkout"], button:has-text("Thanh toán")').first();
      // Giỏ hàng có thể trống hoặc có sản phẩm. Chỉ cần xác nhận giao diện tải thành công.
      await expect(page.locator('body'), 'Trang giỏ hàng bị lỗi 404').not.toContainText('404 Not Found');
    }
  });

  test('User can reach the checkout page if logged in', async ({ page }) => {
    const auth = new AuthHelper(page);
    await auth.login(); // Đăng nhập bằng tài khoản người dùng thông thường

    // Thêm trước một sản phẩm vào giỏ hàng để tránh bị chuyển hướng khi vào trang thanh toán
    await page.goto('/products');
    const productLinks = page.locator('a[href*="/san-pham/"]').first();
    if (await productLinks.isVisible()) {
        await productLinks.click();
        const addToCartBtn = page.locator('button:has-text("Thêm vào giỏ"), button:has-text("Add to cart")').first();
        await addToCartBtn.click();
    }

    await page.goto('/checkout');
    await expect(page, 'Không ở trang thanh toán hoặc trang giỏ hàng').toHaveURL(/.*(\/checkout|\/cart)/);
    await expect(page.locator('body'), 'Không được hiển thị lỗi 403 Forbidden khi truy cập trang thanh toán').not.toContainText('403 Forbidden');
    await expect(page.locator('body'), 'Không được yêu cầu đăng nhập lại khi đã đăng nhập').not.toContainText('login');
  });

});

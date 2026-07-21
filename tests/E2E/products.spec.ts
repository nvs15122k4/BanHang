import { test, expect } from '@playwright/test';

test.describe('Product and Shopping Features', () => {

  test('User can view homepage and navigate to products', async ({ page }) => {
    // Điều hướng đến trang chủ
    await page.goto('/');
    await expect(page, 'Tiêu đề trang chủ phải chứa "Sàn Tím Vi En"').toHaveTitle(/Sàn Tím Vi En/i); // Kiểm tra tiêu đề đặc trưng

    // Điều hướng đến trang sản phẩm
    await page.goto('/products');
    await expect(page.locator('body'), 'Trang sản phẩm phải chứa từ khóa "Sản phẩm" hoặc "Products"').toContainText(/Sản phẩm|Products/i);
  });

  test('User can view a product detail page', async ({ page }) => {
    await page.goto('/products');
    
    // Tìm liên kết sản phẩm đầu tiên
    const productLinks = page.locator('a[href*="/san-pham/"]').first();
    
    // Nếu có sản phẩm trên trang, nhấp vào sản phẩm đầu tiên
    if (await productLinks.isVisible()) {
      await productLinks.click();
      
      // Đảm bảo URL thay đổi sang URL chi tiết sản phẩm
      await expect(page, 'Không chuyển hướng đến trang chi tiết sản phẩm').toHaveURL(/.*\/san-pham\/.*/);
      
      // Đảm bảo nút thêm vào giỏ hàng hiển thị
      const addToCartBtn = page.locator('button:has-text("Thêm vào giỏ"), button:has-text("Add to cart")').first();
      await expect(addToCartBtn, 'Nút thêm vào giỏ hàng phải hiển thị').toBeVisible();
    }
  });

});

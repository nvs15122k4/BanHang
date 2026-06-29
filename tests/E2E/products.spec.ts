import { test, expect } from '@playwright/test';

test.describe('Product and Shopping Features', () => {

  test('User can view homepage and navigate to products', async ({ page }) => {
    // Navigate to homepage
    await page.goto('/');
    await expect(page).toHaveTitle(/Sàn Tím Vi En/i); // Checking typical title

    // Navigate to products page
    await page.goto('/products');
    await expect(page.locator('body')).toContainText(/Sản phẩm|Products/i);
  });

  test('User can view a product detail page', async ({ page }) => {
    await page.goto('/products');
    
    // Find the first product link
    const productLinks = page.locator('a[href*="/san-pham/"]').first();
    
    // If there are products on the page, click the first one
    if (await productLinks.isVisible()) {
      await productLinks.click();
      
      // Ensure the URL changed to a product detail URL
      await expect(page).toHaveURL(/.*\/san-pham\/.*/);
      
      // Ensure the Add to cart button is present
      const addToCartBtn = page.locator('button:has-text("Thêm vào giỏ"), button:has-text("Add to cart")').first();
      await expect(addToCartBtn).toBeVisible();
    }
  });

});

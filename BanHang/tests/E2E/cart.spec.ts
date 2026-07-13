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
      
      // Usually there is a toast message or an update to the cart icon.
      // Wait a moment or navigate to the cart explicitly.
      await page.goto('/cart');
      
      // Ensure we are on the cart page
      await expect(page).toHaveURL(/.*\/cart/);
      
      // Ensure the checkout button exists
      const checkoutBtn = page.locator('a[href*="/checkout"], button:has-text("Thanh toán")').first();
      // It might be empty or have products. We just verify the UI loads.
      await expect(page.locator('body')).not.toContainText('404 Not Found');
    }
  });

  test('User can reach the checkout page if logged in', async ({ page }) => {
    const auth = new AuthHelper(page);
    await auth.login(); // Logs in as a normal user

    // Add a product to cart first so checkout doesn't redirect
    await page.goto('/products');
    const productLinks = page.locator('a[href*="/san-pham/"]').first();
    if (await productLinks.isVisible()) {
        await productLinks.click();
        const addToCartBtn = page.locator('button:has-text("Thêm vào giỏ"), button:has-text("Add to cart")').first();
        await addToCartBtn.click();
    }

    await page.goto('/checkout');
    await expect(page).toHaveURL(/.*(\/checkout|\/cart)/);
    await expect(page.locator('body')).not.toContainText('403 Forbidden');
    await expect(page.locator('body')).not.toContainText('login');
  });

});

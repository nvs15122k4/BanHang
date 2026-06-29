import { test, expect } from '@playwright/test';
import { AuthHelper } from './helpers/auth';

test.describe('Authentication Features', () => {

  test('User can login and logout successfully', async ({ page }) => {
    const auth = new AuthHelper(page);
    
    // Perform login
    await auth.login();

    // Now try to logout
    // Assume there's a logout button or form in the UI
    // Sometimes it's in a dropdown
    await page.goto('/profile'); // or dashboard
    
    // Usually Laravel logout is a POST form. We might find a submit button for logout
    const logoutBtn = page.locator('button:has-text("Đăng xuất"), a:has-text("Đăng xuất"), form[action$="logout"] button').first();
    
    if (await logoutBtn.isVisible()) {
      await logoutBtn.click();
      
      // Should be redirected to home or login after logout
      await expect(page).toHaveURL(/.*(\/|\/login)$/);
    } else {
        // Fallback: If logout button isn't visible, directly submit the logout form if it exists
        const logoutForm = page.locator('form[action$="logout"]').first();
        if (await logoutForm.count() > 0) {
            await logoutForm.evaluate((form: HTMLFormElement) => form.submit());
            await expect(page).toHaveURL(/.*(\/|\/login)$/);
        }
    }
  });

  test('User can access profile page when logged in', async ({ page }) => {
    const auth = new AuthHelper(page);
    await auth.login();

    await page.goto('/profile');
    
    // Ensure we are on the profile page and not redirected to login
    await expect(page).toHaveURL(/.*\/profile/);
    await expect(page.locator('body')).not.toContainText('403 Forbidden');
    await expect(page.locator('body')).toContainText(/Thông tin cá nhân|Tài khoản của tôi/i);
  });

});

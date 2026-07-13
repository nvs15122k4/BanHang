import { Page, expect } from '@playwright/test';

/**
 * What does this file do?
 * Provides reusable authentication functions for Playwright E2E tests.
 * Primary purpose is to avoid rewriting the login flow in every test.
 */

export class AuthHelper {
    readonly page: Page;

    constructor(page: Page) {
        this.page = page;
    }

    /**
     * Login to the application.
     * @param email User's email
     * @param password User's password
     */
    async login(email?: string, password?: string) {
        const loginEmail = email || process.env.PLAYWRIGHT_USER_EMAIL || 'user@example.com';
        const loginPassword = password || process.env.PLAYWRIGHT_USER_PASSWORD || 'password';
        const loginUrl = process.env.PLAYWRIGHT_BASE_URL 
            ? `${process.env.PLAYWRIGHT_BASE_URL}/login` 
            : '/login';

        await this.page.goto(loginUrl);
        await this.page.locator('input[name="email"]').fill(loginEmail);
        await this.page.locator('input[name="password"]').fill(loginPassword);

        // Standard Laravel Breeze/Jetstream login submit
        await this.page.locator('button[type="submit"]:has-text("ĐĂNG NHẬP"), button[type="submit"]:has-text("Đăng nhập"), button[type="submit"]:has-text("Login")').first().click();
        
        // Assert successful login by checking URL or main page
        await expect(this.page).not.toHaveURL(/.*login/);
    }

    /**
     * Login as an Admin using environment variables.
     */
    async loginAsAdmin() {
        const adminEmail = process.env.PLAYWRIGHT_ADMIN_EMAIL || 'khanhtrung778@gmail.com';
        const adminPassword = process.env.PLAYWRIGHT_ADMIN_PASSWORD || '123456';
        await this.login(adminEmail, adminPassword);
    }

    /**
     * Navigate to Admin Dashboard.
     */
    async navigateToAdminDashboard() {
        const adminUrl = process.env.PLAYWRIGHT_ADMIN_URL || '/admin/dashboard';
        await this.page.goto(adminUrl);
        
        // Ensure no 403 Forbidden
        await expect(this.page.locator('body')).not.toContainText('403 Forbidden');
        // Simple assertion to verify the page loads
        await expect(this.page).toHaveURL(new RegExp('.*' + adminUrl.replace('https://testing.hkspace.hoangkhang.com.vn', '')));
    }
}

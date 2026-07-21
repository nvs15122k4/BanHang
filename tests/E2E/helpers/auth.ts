import { Page, expect } from '@playwright/test';

/**
 * File này làm nhiệm vụ gì?
 * Cung cấp các hàm xác thực có thể tái sử dụng cho các bài kiểm thử E2E Playwright.
 * Mục đích chính là tránh lặp lại logic đăng nhập trong từng file kiểm thử.
 */

export class AuthHelper {
    readonly page: Page;

    constructor(page: Page) {
        this.page = page;
    }

    /**
     * Đăng nhập vào ứng dụng.
     * @param email Email của người dùng
     * @param password Mật khẩu của người dùng
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

        // Nhấp nút gửi form đăng nhập chuẩn của Laravel Breeze/Jetstream
        await this.page.locator('button[type="submit"]:has-text("ĐĂNG NHẬP"), button[type="submit"]:has-text("Đăng nhập"), button[type="submit"]:has-text("Login")').first().click();
        
        // Xác nhận đăng nhập thành công bằng cách kiểm tra URL không còn ở trang đăng nhập
        await expect(this.page, 'Không được ở lại trang đăng nhập sau khi đăng nhập thành công').not.toHaveURL(/.*login/);
    }

    /**
     * Đăng nhập với vai trò Quản trị viên (Admin) sử dụng biến môi trường.
     */
    async loginAsAdmin() {
        const adminEmail = process.env.PLAYWRIGHT_ADMIN_EMAIL || 'khanhtrung778@gmail.com';
        const adminPassword = process.env.PLAYWRIGHT_ADMIN_PASSWORD || '123456';
        await this.login(adminEmail, adminPassword);
    }

    /**
     * Điều hướng tới trang quản trị admin (Admin Dashboard).
     */
    async navigateToAdminDashboard() {
        const adminUrl = process.env.PLAYWRIGHT_ADMIN_URL || '/admin/dashboard';
        await this.page.goto(adminUrl);
        
        // Đảm bảo không gặp lỗi 403 Forbidden
        await expect(this.page.locator('body'), 'Không được hiển thị lỗi 403 Forbidden khi truy cập trang quản trị').not.toContainText('403 Forbidden');
        // Xác nhận đơn giản để xác minh trang tải thành công
        await expect(this.page, 'Địa chỉ URL của trang quản trị không khớp với mong đợi').toHaveURL(new RegExp('.*' + adminUrl.replace('https://testing.hkspace.hoangkhang.com.vn', '')));
    }
}

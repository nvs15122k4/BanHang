import { test, expect } from '@playwright/test';
import { AuthHelper } from './helpers/auth';

/**
 * File này làm nhiệm vụ gì?
 * Xác minh tài khoản quản trị viên (admin) đăng nhập thành công và truy cập được vào trang quản trị admin.
 * Mục đích chính là kiểm thử nhanh (smoke test) luồng hoạt động chuẩn từ lúc đăng nhập đến trang quản trị.
 */

test.describe('Admin Authentication and Dashboard Access', () => {
    test('User logs in and navigates to admin dashboard', async ({ page }) => {
        const auth = new AuthHelper(page);

        // Bước: Đăng nhập với vai trò Admin
        await auth.loginAsAdmin();

        // Bước: Điều hướng trực tiếp tới trang quản trị admin
        await auth.navigateToAdminDashboard();
    });
});

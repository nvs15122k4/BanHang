import { test, expect } from '@playwright/test';
import { AuthHelper } from './helpers/auth';

/**
 * What does this file do?
 * Verifies that an admin user can successfully log in and access the admin management dashboard.
 * Primary purpose is a basic happy-path smoke test from user login to admin panel.
 */

test.describe('Admin Authentication and Dashboard Access', () => {
    test('User logs in and navigates to admin dashboard', async ({ page }) => {
        const auth = new AuthHelper(page);

        // Act: Login as Admin
        await auth.loginAsAdmin();

        // Act: Navigate explicitly to the admin dashboard
        await auth.navigateToAdminDashboard();
    });
});

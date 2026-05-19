import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/utilities.css',
                'resources/css/airbnb-style.css',
                'resources/css/admin.css',
                'resources/css/admin_common.css',
                'resources/css/admin_layout.css',
                'resources/css/auth.css',
                'resources/css/extracted-inline.css',
                'resources/css/products.css',
                'resources/css/views/home.css',
                'resources/css/views/product_index.css',
                'resources/css/views/product_show.css',
                'resources/css/views/cart.css',
                'resources/css/views/checkout.css',
                'resources/css/views/profile.css',
                'resources/css/views/order_index.css',
                'resources/css/views/order_show.css',
                'resources/css/views/notifications.css',
                'resources/css/views/static_pages.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});

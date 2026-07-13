import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'public/css/app.css',
                'public/css/airbnb-style.css',
                'public/css/admin.css',
                'public/css/admin_common.css',
                'public/css/admin_layout.css',
                'public/css/auth.css',
                'public/css/extracted-inline.css',
                'public/css/products.css',
                'public/css/views/home.css',
                'public/css/views/product_index.css',
                'public/css/views/product_show.css',
                'public/css/views/cart.css',
                'public/css/views/checkout.css',
                'public/css/views/profile.css',
                'public/css/views/order_index.css',
                'public/css/views/order_show.css',
                'public/css/views/notifications.css',
                'public/css/views/static_pages.css',
                'public/js/app.js',
            ],
            refresh: true,
        }),
    ],
});

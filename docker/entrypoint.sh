#!/bin/sh
# Clear cached config so runtime env (APP_KEY etc.) from Render is respected
php /var/www/html/artisan config:clear --quiet

# Render free tier storage là ephemeral → redirect cache/views/sessions về /tmp.
# QUAN TRỌNG: /tmp/storage phải chown cho www-data — Apache/PHP chạy dưới user này.
# Nếu để root (entrypoint chạy như root), www-data không ghi được →
# Filesystem::put() gọi tempnam() fail → 500 "file created in the system's temporary directory".
mkdir -p /tmp/storage/framework/cache/data \
         /tmp/storage/framework/views \
         /tmp/storage/framework/sessions
chown -R www-data:www-data /tmp/storage

# Đảm bảo storage chính cũng có + chown (file cache fallback nếu CACHE_PATH unset)
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/logs
chown -R www-data:www-data /var/www/html/storage

exec docker-php-entrypoint "$@"

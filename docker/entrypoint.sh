#!/bin/sh
# Clear cached config so runtime env (APP_KEY etc.) from Render is respected
php /var/www/html/artisan config:clear --quiet

# Tạo thư mục writable cho cache/session/views nếu storage bị read-only
# (Render free tier dùng filesystem ephemeral → redirect cache về /tmp)
mkdir -p /tmp/storage/framework/cache/data \
         /tmp/storage/framework/views \
         /tmp/storage/framework/sessions

# Đảm bảo storage chính cũng có (nếu dùng file/session thật)
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/logs

exec docker-php-entrypoint "$@"

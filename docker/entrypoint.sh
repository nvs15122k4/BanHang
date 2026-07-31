#!/bin/sh
# Clear cached config so runtime env (APP_KEY etc.) from Render is respected
php /var/www/html/artisan config:clear --quiet
exec docker-php-entrypoint "$@"

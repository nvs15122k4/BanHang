#!/bin/bash
set -e

echo "🏗️  Building for Vercel deployment..."

# Install PHP dependencies (production)
echo "📦 Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction --prefer-dist

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --force --no-interaction
fi

# Install Node dependencies
echo "📦 Installing NPM dependencies..."
npm ci --production=false

# Build frontend assets
echo "🎨 Building frontend assets..."
npm run build

# Laravel optimizations
echo "⚡ Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create necessary directories
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

# Set permissions
chmod -R 775 storage bootstrap/cache

echo "✅ Build complete!"

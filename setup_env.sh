#!/bin/bash

# ======================================================================
# FILE: setup_env.sh
# MỤC ĐÍCH: Cài đặt toàn bộ môi trường và khởi chạy dự án "BanHang"
# HỆ ĐIỀU HÀNH HỖ TRỢ: Ubuntu / Debian (Linux)
# ======================================================================
# THÔNG TIN MÔI TRƯỜNG ĐƯỢC LẤY TỪ DỰ ÁN (Project Requirements):
# 1. PHP: Phiên bản 8.3 (Cùng các extension: gd, intl, bcmath, exif, pcntl, pdo_mysql, zip, xml, mbstring, curl)
# 2. Node.js: Phiên bản 22.12.0 (hoặc mới nhất của nhánh 22.x)
# 3. MySQL: Phiên bản 8.x
# 4. Composer: Phiên bản 2.x
# 5. Khác: git, unzip, curl
# ======================================================================

set -e # Dừng script nếu có lỗi xảy ra

echo "======================================="
echo "BẮT ĐẦU CÀI ĐẶT MÔI TRƯỜNG CHO DỰ ÁN"
echo "======================================="

# 1. Cập nhật hệ thống
echo "=> Đang cập nhật danh sách gói phần mềm..."
sudo apt-get update && sudo apt-get upgrade -y

# 2. Cài đặt các công cụ cơ bản
echo "=> Cài đặt các công cụ cơ bản (curl, git, unzip, software-properties-common)..."
sudo apt-get install -y curl git unzip software-properties-common

# 3. Cài đặt PHP 8.3 và các Extensions
echo "=> Thêm repository cho PHP 8.3 (Ondrej)..."
sudo add-apt-repository ppa:ondrej/php -y
sudo apt-get update

echo "=> Cài đặt PHP 8.3 và các extensions cần thiết..."
sudo apt-get install -y php8.3 \
    php8.3-cli php8.3-fpm php8.3-mysql php8.3-zip php8.3-gd php8.3-mbstring \
    php8.3-curl php8.3-xml php8.3-bcmath php8.3-intl php8.3-exif

# 4. Cài đặt Node.js 22.x
echo "=> Cài đặt Node.js (Phiên bản 22.x theo cấu hình của dự án)..."
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt-get install -y nodejs
echo "=> Phiên bản Node.js đã cài: $(node -v)"
echo "=> Phiên bản NPM đã cài: $(npm -v)"

# 5. Cài đặt Composer
echo "=> Cài đặt Composer (Phiên bản 2)..."
EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL_CHECKSUM="$(php -r 'echo hash_file("sha384", "composer-setup.php");')"

if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]; then
    echo >&2 'LỖI: Chữ ký Composer không hợp lệ!'
    rm composer-setup.php
    exit 1
fi
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php
echo "=> Phiên bản Composer đã cài: $(composer -v | head -n 1)"

# 6. Cài đặt MySQL Server
echo "=> Cài đặt MySQL Server (Phiên bản 8)..."
sudo apt-get install -y mysql-server

# Khởi động và cho phép MySQL chạy cùng hệ thống
sudo systemctl start mysql
sudo systemctl enable mysql

echo "=> Cấu hình Database 'banhang'..."
# Lưu ý: Script tự động tạo database tên là 'banhang' và set password mặc định là 'root' cho user 'root'
sudo mysql -e "CREATE DATABASE IF NOT EXISTS banhang CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root'; FLUSH PRIVILEGES;" || true

# 7. Thiết lập dự án Laravel
echo "======================================="
echo "TIẾN HÀNH THIẾT LẬP DỰ ÁN LARAVEL"
echo "======================================="

echo "=> Copy file cấu hình môi trường (.env.example -> .env)..."
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Cập nhật thông tin kết nối DB trong .env (thay đổi giá trị mặc định cho phù hợp với MySQL vừa cài)
sed -i 's/DB_DATABASE=laravel/DB_DATABASE=banhang/' .env
sed -i 's/DB_PASSWORD=/DB_PASSWORD=root/' .env

echo "=> Cài đặt các thư viện PHP (Composer install)..."
composer install --no-interaction --prefer-dist --optimize-autoloader

echo "=> Tạo Application Key..."
php artisan key:generate

echo "=> Chạy Migration và Seeder (Tạo bảng và dữ liệu mẫu)..."
php artisan migrate --force
php artisan db:seed --force

echo "=> Liên kết thư mục Storage..."
php artisan storage:link || true

# 8. Thiết lập Frontend
echo "=> Cài đặt các thư viện Node.js (NPM install)..."
npm ci

echo "=> Build giao diện Frontend bằng Vite..."
npm run build

echo "======================================="
echo "HOÀN TẤT CÀI ĐẶT!"
echo "======================================="
echo "Môi trường đã cài đặt gồm:"
echo "- PHP: $(php -r 'echo PHP_VERSION;')"
echo "- Node.js: $(node -v)"
echo "- MySQL: $(mysql -V)"
echo "- Composer: $(composer -v | head -n 1)"
echo ""
echo "Bạn có thể khởi động server ngay bây giờ bằng lệnh:"
echo "php artisan serve"
echo ""
echo "Tài khoản admin mẫu: admin@gmail.com / 123456"

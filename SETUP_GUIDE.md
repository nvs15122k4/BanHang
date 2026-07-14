# Hướng Dẫn Thiết Lập Toàn Bộ Môi Trường (Setup Guide)

Tài liệu này hướng dẫn chi tiết cách thiết lập toàn bộ môi trường từ A-Z cho dự án **BanHang** (Laravel + Vue/Blade + Tailwind + MySQL).

Bạn có thể chọn 1 trong 2 cách thiết lập sau:
- **Cách 1: Sử dụng Docker (Khuyên dùng)** - Nhanh chóng, không lo xung đột phiên bản phần mềm.
- **Cách 2: Cài đặt thủ công (Local)** - Dành cho việc phát triển sâu, nếu bạn đã có sẵn PHP, Composer, Node.js trên máy.

---

## 1. Yêu Cầu Hệ Thống Cần Thiết (Prerequisites)

Dù cài bằng cách nào, máy của bạn cũng cần có tối thiểu các công cụ sau:
- **Git**: Để clone và quản lý mã nguồn.
- **Trình duyệt**: Google Chrome, Edge hoặc Firefox.
- **Editor**: VS Code hoặc PHPStorm.

**Nếu bạn chọn Cài đặt thủ công (Local), bạn cần bổ sung:**
- **PHP**: Phiên bản 8.3 trở lên.
- **Composer**: Phiên bản 2.x.
- **Node.js**: Phiên bản 20.x hoặc 22.x (Kiểm tra bằng `node -v`).
- **NPM**: Phiên bản 10.x trở lên.
- **MySQL**: Phiên bản 8.0 trở lên.

**Nếu bạn chọn Docker, bạn cần:**
- **Docker Desktop** (trên Windows/Mac) hoặc **Docker Engine** (trên Linux).
- **Docker Compose** (thường đã tích hợp sẵn trong Docker Desktop).

---

## 2. Cách 1: Chạy Dự Án Bằng Docker (Khuyên Dùng)

Sử dụng Docker giúp giả lập toàn bộ server (PHP, Nginx, MySQL) vào trong các "container", bạn không cần phải cài trực tiếp các database hay runtime này lên máy tính cá nhân.

### Bước 2.1: Clone dự án và cấu hình biến môi trường
Mở Terminal / Command Prompt tại thư mục bạn muốn chứa code:

```bash
# Clone dự án (nếu bạn chưa clone)
git clone <url-repo-cua-ban>
cd San-tim-vien

# Copy file biến môi trường cho Docker
cp .env.docker.example .env.docker
```

Mở file `.env.docker` vừa tạo bằng Editor của bạn, kiểm tra các thông số kết nối Database (thường không cần sửa gì vì đã được cấu hình mặc định cho Docker).

### Bước 2.2: Khởi chạy các container
Tại thư mục gốc của dự án, chạy lệnh:

```bash
docker compose --env-file .env.docker up -d --build
```
*Lệnh này sẽ tải các image (PHP, Nginx, MySQL) và khởi động chúng ngầm (chế độ detached `-d`). Lần đầu chạy sẽ mất vài phút.*

### Bước 2.3: Cài đặt dependencies và sinh khóa
Chạy các lệnh sau bên trong container `app`:

```bash
# Cài đặt thư viện PHP (Composer)
docker compose --env-file .env.docker exec app composer install

# Tạo APP_KEY
docker compose --env-file .env.docker exec app php artisan key:generate
```

### Bước 2.4: Khởi tạo cơ sở dữ liệu
```bash
# Chạy migration để tạo bảng
docker compose --env-file .env.docker exec app php artisan migrate

# (Tùy chọn) Chạy Seeder để tạo dữ liệu mẫu (Sản phẩm, Admin user)
docker compose --env-file .env.docker exec app php artisan db:seed
```

### Bước 2.5: Build giao diện Frontend (Vite)
Mở một terminal khác và chạy server giao diện (Vite dev server) bằng Docker:

```bash
docker compose --env-file .env.docker --profile dev up vite
```

### 🎉 Hoàn tất (Docker)
- Website của bạn hiện đang chạy tại: [http://localhost:8080](http://localhost:8080)
- Vite dev server đang chạy tại: [http://localhost:5173](http://localhost:5173)

---

## 3. Cách 2: Cài Đặt Thủ Công (Chạy Local)

Nếu bạn đã quen với PHP và Node.js, bạn có thể chạy trực tiếp dự án mà không cần Docker.

### Bước 3.1: Copy file biến môi trường
```bash
cp .env.example .env
```

### Bước 3.2: Khởi tạo Database (MySQL)
- Mở công cụ quản lý MySQL của bạn (như phpMyAdmin, DBeaver, DataGrip hoặc MySQL CLI).
- Tạo một database mới tên là `banhang` (hoặc tên bất kỳ bạn thích).
- Mở file `.env` vừa copy, sửa các thông số sau cho đúng với thông tin máy bạn:
  ```env
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=banhang
  DB_USERNAME=root     # Tên đăng nhập mysql của bạn
  DB_PASSWORD=         # Mật khẩu mysql của bạn
  ```

### Bước 3.3: Cài đặt Dependencies và cấu hình
Chạy lần lượt các lệnh sau trên terminal tại thư mục gốc:

```bash
# 1. Cài đặt các thư viện PHP
composer install

# 2. Sinh khóa bảo mật cho ứng dụng
php artisan key:generate

# 3. Tạo các bảng trong Database
php artisan migrate

# 4. (Tùy chọn) Thêm dữ liệu mẫu
php artisan db:seed

# 5. Link thư mục chứa hình ảnh public
php artisan storage:link
```

### Bước 3.4: Cài đặt Frontend (Tailwind / Vite)
Cài đặt thư viện Node.js và chạy Vite server:

```bash
npm install
npm run dev
```

### Bước 3.5: Khởi động Backend server
Mở một terminal **thứ hai** (giữ terminal Vite đang chạy) và gõ:

```bash
php artisan serve
```

### 🎉 Hoàn tất (Thủ công)
- Website của bạn hiện đang chạy tại: [http://localhost:8000](http://localhost:8000) (hoặc 127.0.0.1:8000)

---

## 4. Tài khoản Admin mẫu

Nếu bạn đã chạy lệnh sinh dữ liệu mẫu (`php artisan db:seed`), bạn có thể đăng nhập vào trang quản trị với thông tin sau:
- **Email**: `admin@gmail.com`
- **Mật khẩu**: `123456`

---

## 5. Cấu Hình Kiểm Thử Cuối Đến Cuối (E2E Tests) với Playwright

Dự án này sử dụng Playwright để chạy các bài test trình duyệt.

```bash
# 1. Cài đặt dependencies Node (nếu chưa làm)
npm install

# 2. Cài đặt các trình duyệt cho Playwright
npx playwright install --with-deps

# 3. Chạy toàn bộ các bài test E2E
npx playwright test

# 4. (Tùy chọn) Chạy test ở chế độ có giao diện (UI mode) để dễ debug
npx playwright test --ui
```

---

## 6. Xử lý sự cố thường gặp (Troubleshooting)

1. **Lỗi 500 Server Error hoặc Permission Denied (Lỗi quyền truy cập file - Linux/Mac)**:
   Nếu chạy trên máy Linux/Mac, thư mục `storage` cần được cấp quyền ghi.
   ```bash
   chmod -R 775 storage bootstrap/cache
   # Nếu dùng Docker: docker compose exec app chmod -R 775 storage bootstrap/cache
   ```

2. **Lỗi `SQLSTATE[HY000] [1049] Unknown database`**:
   - Do bạn chưa tạo Database trong MySQL trước khi chạy lệnh `php artisan migrate`. Hãy quay lại bước 3.2 để tạo Database.

3. **Giao diện bị mất CSS / JavaScript**:
   - Bạn quên chạy lệnh `npm run dev` (hoặc vite build). Frontend Vite cần phải được chạy song song với backend.

4. **Trang web báo lỗi "No application encryption key has been specified."**:
   - Bạn chưa chạy lệnh `php artisan key:generate`.

<div align="center">

# BanHang

**Website bán hàng thời trang xây dựng bằng Laravel MVC**

![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-ready-2496ED?style=for-the-badge&logo=docker&logoColor=white)

BanHang mô phỏng một hệ thống thương mại điện tử cho cửa hàng thời trang, gồm đầy đủ luồng mua hàng cho khách và khu vực quản trị cho admin.

</div>

---

## Mục lục

- [Giới thiệu](#giới-thiệu)
- [Chức năng chính](#chức-năng-chính)
- [Luồng hoạt động](#luồng-hoạt-động)
- [Công nghệ sử dụng](#công-nghệ-sử-dụng)
- [Cấu trúc thư mục](#cấu-trúc-thư-mục)
- [Cài đặt thủ công](#cài-đặt-thủ-công)
- [Chạy bằng Docker](#chạy-bằng-docker)
- [Tài khoản admin mẫu](#tài-khoản-admin-mẫu)
- [Lệnh hữu ích](#lệnh-hữu-ích)
- [Ghi chú dữ liệu](#ghi-chú-dữ-liệu)

---

## Giới thiệu

**BanHang** là website bán hàng thời trang được xây dựng theo mô hình **Laravel MVC**. Dự án tập trung vào các chức năng thực tế của một cửa hàng trực tuyến: xem sản phẩm, tìm kiếm, lọc danh mục, thêm vào giỏ hàng, đặt hàng, theo dõi trạng thái đơn hàng, đánh giá sản phẩm và quản lý hệ thống ở phía admin.

Dự án phù hợp cho:

| Nhu cầu | Mô tả |
| --- | --- |
| Đồ án môn học | Có đủ frontend, backend, database, auth, admin và Docker |
| Demo thương mại điện tử | Có luồng mua hàng, quản lý kho, đơn hàng và khuyến mãi |
| Nền tảng mở rộng | Có thể phát triển thêm thanh toán thật, vận chuyển, mã giảm giá nâng cao |

---

## Chức năng chính

### Dành cho khách hàng

| Nhóm chức năng | Chi tiết |
| --- | --- |
| Tài khoản | Đăng ký, xác nhận email, đăng nhập, đăng xuất, đặt lại/đổi mật khẩu |
| Hồ sơ | Cập nhật thông tin cá nhân, ảnh đại diện, chiều cao, cân nặng |
| Địa chỉ | Thêm, sửa, xóa và đặt địa chỉ giao hàng mặc định |
| Sản phẩm | Xem danh sách, tìm kiếm, lọc, xem chi tiết, xem tồn kho |
| Gợi ý size | Gợi ý kích cỡ dựa trên chiều cao, cân nặng và BMI |
| Giỏ hàng | Thêm sản phẩm, cập nhật số lượng, xóa sản phẩm |
| Thanh toán | Tạo đơn hàng từ giỏ hàng và địa chỉ giao hàng |
| Đơn hàng | Theo dõi đơn, xem chi tiết, gửi yêu cầu hủy, gửi thông tin hoàn tiền |
| Wishlist | Lưu sản phẩm yêu thích |
| Đánh giá | Gửi và xóa đánh giá sản phẩm |
| Thông báo | Nhận thông báo về đơn hàng và thanh toán |

### Dành cho admin

| Nhóm chức năng | Chi tiết |
| --- | --- |
| Dashboard | Xem tổng quan hoạt động hệ thống |
| Người dùng | Quản lý tài khoản, role admin/user và trạng thái hoạt động |
| Danh mục | Thêm, sửa, xóa danh mục sản phẩm |
| Sản phẩm | Quản lý sản phẩm, hình ảnh, size, trạng thái và tồn kho |
| Đơn hàng | Xem, tạo, cập nhật trạng thái giao hàng và thanh toán |
| Hủy đơn | Duyệt hoặc từ chối yêu cầu hủy đơn của khách |
| Hoàn tiền | Cập nhật trạng thái và ghi chú hoàn tiền |
| Khuyến mãi | Quản lý chương trình khuyến mãi |
| Kho hàng | Nhập kho, xuất kho, điều chỉnh tồn kho, xem lịch sử kho |
| Báo cáo | Xuất danh sách sản phẩm và thống kê ra Excel |
| Nhật ký thao tác | Xem audit log các thay đổi đã được hệ thống ghi nhận |

---

## Luồng hoạt động

### Luồng mua hàng

```text
Khách hàng đăng nhập
        |
        v
Xem và chọn sản phẩm
        |
        v
Thêm vào giỏ hàng
        |
        v
Kiểm tra giỏ hàng
        |
        v
Chọn địa chỉ giao hàng
        |
        v
Đặt hàng
        |
        v
Theo dõi trạng thái đơn hàng
```

### Luồng quản lý đơn hàng

```text
Admin xem đơn hàng
        |
        v
Cập nhật trạng thái giao hàng
        |
        v
Cập nhật trạng thái thanh toán
        |
        v
Xử lý hủy đơn hoặc hoàn tiền nếu có
```

### Luồng quản lý kho

```text
Nhập kho / Xuất kho / Điều chỉnh kho
        |
        v
Cập nhật số lượng sản phẩm
        |
        v
Ghi lịch sử tồn kho
        |
        v
Đồng bộ trạng thái còn hàng / hết hàng
```

---

## Công nghệ sử dụng

| Thành phần | Công nghệ |
| --- | --- |
| Backend | Laravel 13, PHP 8.3 |
| Frontend | Blade, Tailwind CSS, Alpine.js, Vite |
| Database | MySQL |
| Authentication | Custom Laravel authentication |
| Authorization | Middleware kiểm tra quyền admin |
| API authentication | Laravel Sanctum đã cài; `routes/api.php` hiện chưa được bootstrap nạp |
| Lưu trữ hình ảnh | Cloudinary, local storage fallback |
| Export dữ liệu | Maatwebsite Excel |
| Dev environment | Docker, Nginx, PHP-FPM, MySQL, Node/Vite |

---

## Cấu trúc thư mục

```text
app/
  Http/Controllers/     Controller xử lý request
  Models/               Model Eloquent
  Services/             Business logic dùng chung
  Exports/              Xuất Excel

database/
  migrations/           Cấu trúc database
  seeders/              Dữ liệu mẫu

resources/
  views/                Giao diện Blade

routes/
  web.php               Route giao diện web
  api.php               Định nghĩa API dự kiến, hiện chưa được bootstrap nạp

public/
  images/               Hình ảnh public
  css/                  CSS được Vite build
  js/                   JavaScript frontend được Vite build

docker/
  nginx/                Cấu hình Nginx cho Docker
```

---

## Yêu cầu môi trường

### Chạy trực tiếp trên máy

| Công cụ | Phiên bản gợi ý |
| --- | --- |
| PHP | 8.3+ |
| Composer | 2.x |
| Node.js | 22.12.0+ |
| npm | 10+ |
| MySQL | 8.x |

### Chạy bằng Docker

| Công cụ | Ghi chú |
| --- | --- |
| Docker | Dùng để chạy toàn bộ môi trường |
| Docker Compose | Chạy các service app, nginx, mysql, vite |

---

## Cài đặt thủ công

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run build
php artisan serve
```

Chạy Vite trong môi trường phát triển:

```bash
npm run dev
```

Sau khi chạy `php artisan serve`, website thường mở tại:

```text
http://127.0.0.1:8000
```

---

## Chạy bằng Docker

Tạo file môi trường Docker:

```bash
cp .env.docker.example .env.docker
```

Kiem tra env bang Zod truoc khi chay:

```bash
docker compose --env-file .env.docker run --rm env-check
```

Khi deploy production, dat `APP_ENV=production`, `APP_DEBUG=false`, tao `APP_KEY` that va doi toan bo mat khau database mac dinh. Container se dung som neu env khong hop le.

Khởi động project:

```bash
docker compose --env-file .env.docker up -d --build
```

Chạy migration:

```bash
docker compose --env-file .env.docker exec app php artisan migrate
```

Truy cập website:

```text
http://localhost:8080
```

Vite dev server:

```bash
docker compose --env-file .env.docker --profile dev up vite
```

```text
http://localhost:5173
```

---

## Tài khoản admin mẫu

Dự án có seeder tạo tài khoản admin mẫu cho môi trường phát triển trống. Lệnh seed có ghi dữ liệu vào database; không chạy trên database đang cần bảo toàn dữ liệu/admin khi chưa kiểm tra và xác nhận theo `AGENTS.md`.

```bash
php artisan db:seed
```

Thông tin đăng nhập:

| Vai trò | Email | Mật khẩu |
| --- | --- | --- |
| Admin | `admin@gmail.com` | `123456` |

Nếu chạy bằng Docker:

```bash
docker compose --env-file .env.docker exec app php artisan db:seed
```

---

## Lệnh hữu ích

### Laravel

```bash
php artisan route:list
php artisan storage:link
php artisan cache:clear
php artisan config:clear
```

### Frontend

```bash
npm run dev
npm run build
```

### Docker

```bash
docker compose --env-file .env.docker ps
docker compose --env-file .env.docker exec app php artisan route:list
docker compose --env-file .env.docker exec app php artisan storage:link
docker compose --env-file .env.docker down
```

---

## Ghi chú dữ liệu

> Không chạy các lệnh reset database trên môi trường có dữ liệu thật nếu chưa sao lưu.

- `supportAiAgent/` chứa bản đồ module đã đối chiếu code ngày 2026-05-26, nhưng thư mục này hiện bị `.gitignore` loại trừ.
- `routes/api.php` tồn tại dưới dạng định nghĩa dự kiến; kiểm tra ngày 2026-05-26 bằng `php artisan route:list` cho kết quả không có route `/api/*`.
- Các file `.env` thật không nên đưa lên Git.
- File `.env.docker.example` chỉ là file mẫu để tạo cấu hình Docker riêng.
- Lệnh `docker compose down -v` sẽ xóa volume MySQL của Docker.
- Khi triển khai thật, cần đổi mật khẩu admin mẫu và cấu hình lại `APP_KEY`, database, mail, storage, Cloudinary.

---

<div align="center">

**BanHang - Laravel fashion e-commerce project**

</div>

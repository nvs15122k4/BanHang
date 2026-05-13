# Lưu trữ ảnh sản phẩm trên Google Drive — Tài liệu kỹ thuật

## Mục lục

1. [Tổng quan kiến trúc](#1-tổng-quan-kiến-trúc)
2. [Các thành phần trong hệ thống](#2-các-thành-phần-trong-hệ-thống)
3. [Cấu hình môi trường](#3-cấu-hình-môi-trường)
4. [Flow Upload ảnh](#4-flow-upload-ảnh)
5. [Flow Hiển thị ảnh (Proxy)](#5-flow-hiển-thị-ảnh-proxy)
6. [Flow Xóa ảnh](#6-flow-xóa-ảnh)
7. [Chi tiết từng file](#7-chi-tiết-từng-file)
8. [Tại sao dùng 2 loại xác thực](#8-tại-sao-dùng-2-loại-xác-thực)
9. [Bảo mật](#9-bảo-mật)
10. [Hạn chế và lưu ý](#10-hạn-chế-và-lưu-ý)
11. [Hướng dẫn thiết lập lại từ đầu](#11-hướng-dẫn-thiết-lập-lại-từ-đầu)

---

## 1. Tổng quan kiến trúc

Thay vì lưu ảnh trực tiếp trên server, hệ thống dùng **Google Drive** làm kho lưu trữ ảnh miễn phí. Laravel đóng vai trò **cầu nối trung gian (proxy)** — người dùng không bao giờ biết ảnh đang lấy từ Drive.

```
┌──────────────────────────────────────────────────────────────────┐
│                       KIẾN TRÚC TỔNG QUAN                        │
├──────────────────────────────────────────────────────────────────┤
│                                                                   │
│  UPLOAD (Admin tạo sản phẩm)                                      │
│  ─────────────────────────────────────────────────────────────   │
│  [Admin Form]                                                     │
│      │ POST /products (multipart/form-data)                       │
│      ▼                                                            │
│  [ProductController] → [ProductService] → [GoogleDriveService]   │
│                                                  │                │
│                                           OAuth2 (tài khoản      │
│                                           khanhtrung778@gmail)    │
│                                                  │                │
│                                           [Google Drive API]      │
│                                                  │                │
│                                           Lưu vào BanHangImage/   │
│                                                  │                │
│                                           Trả về file_id          │
│                                                  │                │
│  [MySQL] ←── lưu file_id vào cột `anh` ──────────┘               │
│                                                                   │
│  HIỂN THỊ (Browser xem ảnh)                                       │
│  ─────────────────────────────────────────────────────────────   │
│  [Blade] → $product->image_path → "/anh/{fileId}"                │
│      │                                                            │
│      ▼                                                            │
│  [Browser] GET /anh/{fileId}                                      │
│      │                                                            │
│      ▼                                                            │
│  [AnhController::proxy()]                                         │
│      │ Kiểm tra fileId có trong DB không?                         │
│      │                                                            │
│      ▼                                                            │
│  [GoogleDriveService::getFileContent()]                           │
│      │ Service Account → Drive API → tải binary ảnh              │
│      │                                                            │
│      ▼                                                            │
│  [Browser] ← response(binary, Content-Type: image/jpeg)          │
│                                                                   │
└──────────────────────────────────────────────────────────────────┘
```

**Điểm mấu chốt:**
- DB chỉ lưu `file_id` ngắn (ví dụ: `1aEv6-d4y89aZ1R4P6g4lLpw3oAA0dyVy`)
- Ảnh trên Drive **không public** — không ai truy cập trực tiếp được
- Mọi request ảnh đều đi qua Laravel proxy `/anh/{fileId}`

---

## 2. Các thành phần trong hệ thống

### 2.1 Google Cloud — 2 loại xác thực

| Loại | Dùng cho | File credentials |
|------|----------|-----------------|
| **Service Account** | Đọc ảnh (proxy) | `storage/app/service-account.json` |
| **OAuth2 Client** | Upload / Xóa ảnh | `storage/app/oauth2-credentials.json` |

### 2.2 Google Drive — Folder BanHangImage

```
Google Drive (khanhtrung778@gmail.com)
└── BanHangImage/  (ID: 1XsflqMmqJHZM1y06_y-EDUqxFK9wRI2q)
    ├── 1746518400_product1.jpg   ← file_id: 1BWivbgbr5NtyaTx4yj00Yf70WsG5R7V6
    ├── 1746518401_product2.png   ← file_id: 1dScRN2YnTWElyF7kGHgUD5hqVQiiPdc4
    └── ...
```

Folder này:
- **Không public** (Quyền truy cập chung: Hạn chế)
- Được share cho Service Account với quyền **Editor**
- File upload vào đây thuộc sở hữu `khanhtrung778@gmail.com`

### 2.3 MySQL — Bảng products

```
┌────┬──────────────────┬────────────────────────────────────┐
│ id │ ten_sp           │ anh (file_id của Drive)            │
├────┼──────────────────┼────────────────────────────────────┤
│ 1  │ Áo thun xanh     │ 1BWivbgbr5NtyaTx4yj00Yf70WsG5R7V6 │
│ 2  │ Quần jean đen    │ 1dScRN2YnTWElyF7kGHgUD5hqVQiiPdc4 │
└────┴──────────────────┴────────────────────────────────────┘
```

Cột `anh` lưu **file_id thuần túy** — không phải URL, không phải tên file.

---

## 3. Cấu hình môi trường

File `.env` cần có các biến sau:

```env
# Service Account (đọc ảnh qua proxy)
GOOGLE_DRIVE_CREDENTIALS=storage/app/service-account.json
GOOGLE_DRIVE_FOLDER_ID=1XsflqMmqJHZM1y06_y-EDUqxFK9wRI2q

# OAuth2 (upload / xóa ảnh)
GOOGLE_OAUTH_CLIENT_ID=407023513945-xxxx.apps.googleusercontent.com
GOOGLE_OAUTH_CLIENT_SECRET=GOCSPX-xxxxxxxxxxxx
GOOGLE_OAUTH_REFRESH_TOKEN=1//0ggjAFimzJbRu...
```

### Cách lấy GOOGLE_DRIVE_FOLDER_ID

Mở folder trên drive.google.com, lấy chuỗi ID trong URL:

```
https://drive.google.com/drive/folders/1XsflqMmqJHZM1y06_y-EDUqxFK9wRI2q
                                        ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
                                        đây là FOLDER_ID
```

### Cách lấy GOOGLE_OAUTH_REFRESH_TOKEN

Chạy script một lần duy nhất:

```bash
php _get_refresh_token.php
# Mở URL trong browser → đăng nhập khanhtrung778@gmail.com
# Cho phép quyền → copy code → dán vào terminal
# Script tự ghi vào .env
```

---

## 4. Flow Upload ảnh

Khi admin tạo/sửa sản phẩm và chọn file ảnh:

```
[Form HTML]
  <form enctype="multipart/form-data">
    <input type="file" name="anh_file">
  </form>
        │
        ▼
[ProductController::store()]
  $image = $request->file('anh_file')
  $this->productService->createProduct($data, $image)
        │
        ▼
[ProductService::createProduct()]
  if ($image) {
      $data['anh'] = $this->googleDrive->uploadImage($image)
  }
  Product::create($data)
        │
        ▼
[GoogleDriveService::uploadImage()]
  // Dùng $driveUpload (OAuth2 client)

  Bước 1 — Tạo metadata:
    DriveFile([
        'name'    => '1746518400_product.jpg',
        'parents' => ['1XsflqMmqJHZM1y06_y-EDUqxFK9wRI2q'],
    ])

  Bước 2 — Gọi Drive API:
    $drive->files->create($metadata, [
        'data'       => file_get_contents($file->getRealPath()),
        'mimeType'   => 'image/jpeg',
        'uploadType' => 'multipart',
        'fields'     => 'id',
    ])

  Bước 3 — Trả về file_id:
    return "1aEv6-d4y89aZ1R4P6g4lLpw3oAA0dyVy"
        │
        ▼
[MySQL]
  products.anh = '1aEv6-d4y89aZ1R4P6g4lLpw3oAA0dyVy'
```

**Kết quả:** File ảnh nằm trong folder BanHangImage trên Drive của `khanhtrung778@gmail.com`. DB chỉ lưu file_id.

---

## 5. Flow Hiển thị ảnh (Proxy)

### Bước 1 — Blade template lấy URL ảnh

```blade
<img src="{{ $product->image_path }}" alt="{{ $product->ten_sp }}">
```

### Bước 2 — Model accessor tạo proxy URL

```php
// app/Models/Product.php
public function getImagePathAttribute()
{
    if (empty($this->anh)) {
        return asset('images/default-product.svg');
    }

    // file_id Drive: chuỗi > 20 ký tự, chỉ có [a-zA-Z0-9_-]
    if (GoogleDriveService::isGoogleDriveFileId($this->anh)) {
        return route('anh.proxy', $this->anh);
        // → "http://localhost:8000/anh/1aEv6-d4y89aZ1R4P6g4lLpw3oAA0dyVy"
    }

    // URL Drive cũ dạng https://drive.google.com/uc?id=...
    if (GoogleDriveService::isGoogleDriveUrl($this->anh)) {
        $fileId = GoogleDriveService::extractFileId($this->anh);
        return route('anh.proxy', $fileId);
    }

    // File local cũ dạng "anh1.jpg" (backward compatible)
    return asset('storage/products/' . $this->anh);
}
```

### Bước 3 — Browser gọi proxy

```
GET /anh/1aEv6-d4y89aZ1R4P6g4lLpw3oAA0dyVy
```

### Bước 4 — Route xử lý

```php
// routes/web.php
Route::get('/anh/{fileId}', [AnhController::class, 'proxy'])
    ->name('anh.proxy')
    ->where('fileId', '[a-zA-Z0-9_-]+');
```

### Bước 5 — AnhController kiểm tra và lấy ảnh

```php
// app/Http/Controllers/AnhController.php
public function proxy(string $fileId)
{
    // Bảo mật: chỉ cho phép file_id đang dùng trong DB
    $exists = Product::where('anh', $fileId)->exists()
           || Product::whereRaw("anh LIKE ?", ["%id={$fileId}%"])->exists();

    if (!$exists) {
        abort(404);
    }

    $file = $this->driveService->getFileContent($fileId);

    return response($file['content'], 200)
        ->header('Content-Type', $file['mimeType'])
        ->header('Cache-Control', 'private, max-age=3600');
}
```

### Bước 6 — GoogleDriveService lấy nội dung file

```php
// app/Services/GoogleDriveService.php
public function getFileContent(string $fileId): array
{
    // Dùng $driveRead (Service Account)
    $file     = $this->driveRead->files->get($fileId, ['fields' => 'mimeType,name']);
    $response = $this->driveRead->files->get($fileId, ['alt' => 'media']);

    return [
        'mimeType' => $file->getMimeType(),
        'content'  => $response->getBody()->getContents(),
        'name'     => $file->getName(),
    ];
}
```

### Toàn bộ flow hiển thị

```
[Browser] GET /anh/1aEv6-...
    │
    ▼
[AnhController::proxy()]
    │ Product::where('anh', '1aEv6-...')->exists() → TRUE
    │
    ▼
[GoogleDriveService::getFileContent()]
    │ Service Account → Drive API
    │ GET googleapis.com/drive/v3/files/1aEv6-...?alt=media
    │
    ▼
[Google Drive] → trả về binary data
    │
    ▼
[Laravel] response(binary, ['Content-Type' => 'image/jpeg'])
    │
    ▼
[Browser] hiển thị ảnh ✓
```

---

## 6. Flow Xóa ảnh

Khi admin xóa sản phẩm hoặc thay ảnh mới:

```php
// ProductService::deleteProduct()
public function deleteProduct(Product $product): bool
{
    $this->deleteOldImage($product->anh);  // xóa ảnh trước
    return (bool) $product->delete();       // rồi xóa DB record
}

// ProductService::deleteOldImage()
private function deleteOldImage(?string $anh): void
{
    if (empty($anh)) return;

    // file_id Drive hoặc URL Drive → xóa trên Drive
    if (GoogleDriveService::isGoogleDriveFileId($anh)
     || GoogleDriveService::isGoogleDriveUrl($anh)) {
        $fileId = GoogleDriveService::extractFileId($anh);
        $this->googleDrive->deleteFile($fileId);
        return;
    }

    // File local cũ → xóa trong storage/
    Storage::disk('public')->delete('products/' . $anh);
}

// GoogleDriveService::deleteFile()
// Dùng $driveUpload (OAuth2) vì cần quyền xóa
public function deleteFile(string $fileId): void
{
    if (!$this->driveUpload) return;
    $this->driveUpload->files->delete($fileId);
}
```

---

## 7. Chi tiết từng file

### `app/Services/GoogleDriveService.php`

Service trung tâm, quản lý toàn bộ tương tác với Google Drive API.

| Method | Xác thực | Mô tả |
|--------|----------|-------|
| `uploadImage(UploadedFile)` | OAuth2 | Upload file, trả về file_id |
| `deleteFile(string $fileId)` | OAuth2 | Xóa file trên Drive |
| `getFileContent(string $fileId)` | Service Account | Lấy binary content |
| `fileExists(string $fileId)` | Service Account | Kiểm tra file tồn tại |
| `isUploadReady(): bool` | — | OAuth2 đã cấu hình chưa |
| `getUploadError(): ?string` | — | Thông báo lỗi OAuth2 |
| `extractFileId(string)` | — | Static: trích xuất file_id |
| `isGoogleDriveFileId(string)` | — | Static: nhận diện file_id |
| `isGoogleDriveUrl(string)` | — | Static: nhận diện URL Drive |

**Khởi tạo 2 client trong constructor:**

```php
public function __construct()
{
    // Client 1: Service Account → chỉ đọc, không bao giờ hết hạn
    $readClient = new Client();
    $readClient->setAuthConfig(base_path(env('GOOGLE_DRIVE_CREDENTIALS')));
    $readClient->addScope(Drive::DRIVE_READONLY);
    $this->driveRead = new Drive($readClient);

    // Client 2: OAuth2 → upload + xóa, dùng quota tài khoản cá nhân
    $uploadClient = new Client();
    $uploadClient->setClientId(env('GOOGLE_OAUTH_CLIENT_ID'));
    $uploadClient->setClientSecret(env('GOOGLE_OAUTH_CLIENT_SECRET'));
    $uploadClient->setAccessType('offline');
    $uploadClient->addScope(Drive::DRIVE_FILE);
    $uploadClient->fetchAccessTokenWithRefreshToken(env('GOOGLE_OAUTH_REFRESH_TOKEN'));
    $this->driveUpload = new Drive($uploadClient);
}
```

### `app/Services/ProductService.php`

Orchestrator — điều phối giữa Drive và DB.

```php
createProduct(array $data, ?UploadedFile $image, ?string $imageUrl): Product
// Nếu có $image  → upload Drive → file_id → $data['anh']
// Nếu có $imageUrl → lưu URL trực tiếp → $data['anh']
// → Product::create($data)

updateProduct(Product $product, array $data, ...): Product
// Nếu có ảnh mới → xóa ảnh cũ trên Drive → upload mới → cập nhật DB

deleteProduct(Product $product): bool
// → xóa ảnh trên Drive → xóa record DB
```

### `app/Models/Product.php`

Accessor `getImagePathAttribute()` tự động chuyển đổi:

```
$product->anh = "1aEv6-d4y89aZ1R4P6g4lLpw3oAA0dyVy"
         ↓
$product->image_path = "http://localhost:8000/anh/1aEv6-d4y89aZ1R4P6g4lLpw3oAA0dyVy"
```

Logic nhận diện theo thứ tự ưu tiên:

```
1. Rỗng                    → asset('images/default-product.svg')
2. Chuỗi > 20 ký tự [a-zA-Z0-9_-]  → route('anh.proxy', $fileId)
3. Chứa drive.google.com   → trích xuất file_id → route('anh.proxy', $fileId)
4. Còn lại                 → asset('storage/products/' . $anh)
```

### `app/Http/Controllers/AnhController.php`

Proxy controller — nhận request từ browser, xác thực, lấy ảnh từ Drive, trả về.

### `routes/web.php`

```php
Route::get('/anh/{fileId}', [AnhController::class, 'proxy'])
    ->name('anh.proxy')
    ->where('fileId', '[a-zA-Z0-9_-]+');
// Regex constraint ngăn path traversal và injection
```

---

## 8. Tại sao dùng 2 loại xác thực

### Vấn đề với Service Account khi upload

Service Account là tài khoản kỹ thuật (robot), **không có Google Drive riêng** và **không có storage quota**. Khi cố upload:

```
Error 403: Service Accounts do not have storage quota.
Leverage shared drives or use OAuth delegation instead.
```

Dù đã share folder cho Service Account với quyền Editor, file upload vẫn cần một owner có quota — Service Account không đáp ứng được.

### Giải pháp: Tách biệt vai trò

```
OAuth2 (khanhtrung778@gmail.com)
├── Upload file → file thuộc sở hữu tài khoản này
├── Xóa file   → có quyền vì là owner
└── Dùng 15GB quota của tài khoản này

Service Account (robot)
├── Đọc file   → được vì folder đã share quyền Editor
├── Không upload/xóa → không cần quota
└── Dùng cho proxy (ổn định hơn, không bao giờ hết hạn)
```

### Tại sao không dùng OAuth2 cho tất cả?

- Refresh token OAuth2 có thể bị thu hồi nếu đổi mật khẩu Google
- Service Account dùng private key — không bao giờ hết hạn
- Proxy đọc ảnh là thao tác thường xuyên → Service Account ổn định hơn

---

## 9. Bảo mật

### Ảnh không public

Folder BanHangImage ở chế độ **Hạn chế** (Restricted):
- Không ai truy cập trực tiếp qua URL Drive được
- Chỉ Service Account (được share quyền) mới đọc được
- User chỉ thấy URL `/anh/1aEv6-...`, không biết nguồn từ Drive

### Kiểm tra DB trước khi proxy

```php
// Chỉ cho phép file_id đang được dùng trong bảng products
$exists = Product::where('anh', $fileId)->exists();
if (!$exists) abort(404);
```

Ngăn kẻ tấn công dùng proxy để đọc file tùy ý trên Drive.

### Route constraint

```php
->where('fileId', '[a-zA-Z0-9_-]+')
```

Chỉ cho phép ký tự hợp lệ — ngăn path traversal, injection qua URL.

### Credentials không commit lên git

Thêm vào `.gitignore`:

```
storage/app/service-account.json
storage/app/oauth2-credentials.json
```

---

## 10. Hạn chế và lưu ý

| Hạn chế | Chi tiết | Cách xử lý |
|---------|----------|------------|
| **Tốc độ** | Mỗi request ảnh gọi Drive API → chậm hơn CDN | Cache `max-age=3600` giảm thiểu |
| **Drive API Quota** | Giới hạn 1 tỷ request/ngày (free) | Đủ dùng cho web nhỏ/vừa |
| **Phụ thuộc Google** | Drive sự cố → web mất ảnh | Thêm fallback ảnh mặc định |
| **Refresh Token** | Bị thu hồi nếu đổi mật khẩu Google | Chạy lại `_get_refresh_token.php` |
| **15GB quota** | Ảnh nhiều có thể đầy | Nén ảnh trước khi upload |

### Cache ảnh

Header `Cache-Control: private, max-age=3600` cho phép browser cache ảnh 1 giờ. Sau 1 giờ browser mới gọi lại proxy. Điều này giảm đáng kể số lần gọi Drive API trong thực tế.

---

## 11. Hướng dẫn thiết lập lại từ đầu

### Bước 1 — Google Cloud Console

Vào `https://console.cloud.google.com` → project `banhangimage-495504`

1. Bật **Google Drive API**
2. Tạo **Service Account** → tạo key JSON → đặt vào `storage/app/service-account.json`
3. Tạo **OAuth2 Client ID** (Desktop app) → download JSON → đặt vào `storage/app/oauth2-credentials.json`
4. OAuth Consent Screen → Test users → thêm `khanhtrung778@gmail.com`

### Bước 2 — Google Drive

1. Tạo folder `BanHangImage` trên Drive của `khanhtrung778@gmail.com`
2. Chuột phải → Share → nhập email Service Account → quyền **Editor**
3. Copy folder ID từ URL

### Bước 3 — Lấy Refresh Token

```bash
cd /home/nvs1512/BanHang
php _get_refresh_token.php
# Mở URL → đăng nhập → copy code → dán vào terminal
# Script tự ghi vào .env
```

### Bước 4 — Cập nhật .env

```env
GOOGLE_DRIVE_CREDENTIALS=storage/app/service-account.json
GOOGLE_DRIVE_FOLDER_ID=<folder_id>
GOOGLE_OAUTH_CLIENT_ID=<client_id>
GOOGLE_OAUTH_CLIENT_SECRET=<client_secret>
GOOGLE_OAUTH_REFRESH_TOKEN=<refresh_token>
```

### Bước 5 — Clear cache và kiểm tra

```bash
php artisan optimize:clear

# Kiểm tra kết nối và liệt kê ảnh
php _list_images.php
```

---

*Tài liệu này mô tả hệ thống tại thời điểm: 06/05/2026*  
*Project: BanHang Laravel — Google Cloud project: `banhangimage-495504`*

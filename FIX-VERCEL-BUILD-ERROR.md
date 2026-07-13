# 🔧 Fix Vercel Build Error: composer command not found

## ❌ Lỗi hiện tại:
```
Command "composer install --no-dev --optimize-autoloader --no-interaction && bash build-assets.sh && php artisan config:cache && php artisan route:cache && php artisan view:cache" exited with 127
```

## 🔍 Nguyên nhân:
Vercel Dashboard có **Build Command override** đang ghi đè `vercel.json` và `package.json` của bạn!

Lệnh cũ (sai) vẫn được lưu trong Vercel Settings và chạy mỗi lần deploy.

---

## ✅ GIẢI PHÁP (4 BƯỚC):

### **BƯỚC 1: Vào Project Settings**
1. Truy cập: https://vercel.com/dashboard
2. Chọn project **"BanHang"** (hoặc tên project của bạn)
3. Click nút **"Settings"** ⚙️ (góc trên)

### **BƯỚC 2: Xóa Build Command**
1. Trong Settings, chọn tab **"General"** (bên trái)
2. Kéo xuống phần **"Build & Development Settings"**
3. Tìm dòng **"Build Command"**
4. Nếu thấy nút **"Override"** → Click vào
5. **❌ XÓA TRỐNG** hộp text (không nhập gì cả)
6. Click **"Save"**

```
Build Command: [                    ]  ← ĐỂ TRỐNG
                ^^^^^^^^^^^^^^^^^^^^
                 Không có gì ở đây!
```

### **BƯỚC 3: Xóa Install Command (nếu có)**
1. Tìm dòng **"Install Command"**
2. Nếu có override → Click và **XÓA TRỐNG**
3. Click **"Save"**

```
Install Command: [                    ]  ← ĐỂ TRỐNG
```

### **BƯỚC 4: Redeploy**
1. Quay lại tab **"Deployments"**
2. Tìm deployment mới nhất
3. Click nút **"..."** (3 chấm) → **"Redeploy"**
4. **QUAN TRỌNG**: Bỏ tick **"Use existing Build Cache"**
5. Click **"Redeploy"**

---

## 🎯 Kết quả mong đợi:

Khi để trống Build Command, Vercel sẽ tự động:

1. ✅ **Detect `composer.json`** → Cài PHP dependencies
2. ✅ **Detect `package.json`** → Chạy script `vercel-build`
3. ✅ **Chạy `vercel-build.sh`**:
   - Install composer packages
   - Install npm packages  
   - Build assets với Vite
   - Optimize Laravel (cache config/routes/views)

### Build Log thành công:
```
[vercel-build] 🔨 Installing Composer dependencies...
[vercel-build] Loading composer repositories with package information
[vercel-build] Installing dependencies...
[vercel-build] ✓ Installed 127 packages

[vercel-build] 📦 Installing npm dependencies...
[vercel-build] added 73 packages

[vercel-build] 🎨 Building assets...
[vercel-build] vite v5.4.21 building for production...
[vercel-build] ✓ built in 609ms

[vercel-build] ⚡ Optimizing Laravel...
[vercel-build] Configuration cached successfully.
[vercel-build] Routes cached successfully.
[vercel-build] Views cached successfully.

[vercel-build] ✅ Build completed successfully!
```

---

## 📋 Checklist:

- [ ] Xóa "Build Command" trong Vercel Settings
- [ ] Xóa "Install Command" trong Vercel Settings (nếu có)
- [ ] Click "Save" để lưu thay đổi
- [ ] Redeploy với "Use existing Build Cache" = OFF
- [ ] Kiểm tra Build Log thấy "vercel-build" script chạy
- [ ] Build thành công ✅

---

## ❓ Nếu vẫn lỗi:

### Option 1: Xóa project và tạo mới
1. Xóa project trong Vercel Dashboard
2. Import lại từ GitHub: https://vercel.com/new
3. Chọn repo: **nvs15122k4/BanHang**
4. **KHÔNG NHẬP** Build Command (để trống!)
5. Thêm Environment Variables (xem DEPLOY-TO-VERCEL.sh)
6. Deploy

### Option 2: Kiểm tra vercel.json
Đảm bảo `vercel.json` KHÔNG có `buildCommand`:

```json
{
  "version": 2,
  "functions": {
    "api/index.php": {
      "runtime": "vercel-php@0.7.2"
    }
  }
  // ❌ KHÔNG CÓ "buildCommand" ở đây!
}
```

### Option 3: Contact support
Nếu vẫn không được, có thể cache bị stuck:
- Clear Vercel cache: Settings → Advanced → Clear Cache
- Hoặc contact Vercel support

---

## 📚 Tài liệu tham khảo:

- Vercel Build Configuration: https://vercel.com/docs/build-step
- Laravel on Vercel: https://vercel.com/guides/deploying-laravel-to-vercel
- Package.json scripts: https://vercel.com/docs/build-step#build-command

---

**🎉 Chúc bạn deploy thành công!**

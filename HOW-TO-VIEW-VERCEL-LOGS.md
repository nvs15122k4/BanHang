# 🔍 HƯỚNG DẪN XEM VERCEL RUNTIME LOGS - TÌM LỖI CHÍNH XÁC

## ⚠️ TẠI SAO CẦN XEM RUNTIME LOGS?

Như bạn đã phân tích đúng, lỗi **FUNCTION_INVOCATION_FAILED** chỉ cho biết:
- ❌ Laravel đã crash
- ❌ Function không thực thi được

Nhưng **KHÔNG cho biết**:
- ❓ Laravel crash vì lý do gì?
- ❓ Thiếu env nào?
- ❓ Database connection lỗi như thế nào?
- ❓ Class nào không tìm thấy?

👉 **Runtime Logs sẽ cho bạn biết LỖI CHÍNH XÁC (error message, stack trace)**

---

## 📋 CÁCH XEM RUNTIME LOGS TRÊN VERCEL DASHBOARD

### Bước 1: Vào Vercel Dashboard
```
https://vercel.com/dashboard
```

### Bước 2: Chọn Project
- Click vào project **BanHang** (hoặc tên project của bạn)

### Bước 3: Vào Tab "Deployments"
- Click tab **Deployments** ở menu trên

### Bước 4: Click vào Deployment Mới Nhất
- Chọn deployment đầu tiên (có commit message: "Fix FUNCTION_INVOCATION_FAILED...")
- Hoặc deployment có status **Failed** hoặc **Ready** (màu đỏ hoặc xanh)

### Bước 5: Xem Runtime Logs
Click vào một trong các tab sau:

#### A. **Runtime Logs** (Quan trọng nhất)
```
Deployments → [Click deployment] → Runtime Logs
```
Sẽ hiển thị:
- ✅ PHP errors (Fatal Error, Class not found, Call to undefined...)
- ✅ Laravel exceptions (Database connection, Missing APP_KEY...)
- ✅ Stack traces chi tiết

#### B. **Build Logs**
```
Deployments → [Click deployment] → Build Logs
```
Sẽ hiển thị:
- ✅ Composer install output
- ✅ Build script errors
- ✅ Missing files warnings

#### C. **Functions Tab**
```
Deployments → [Click deployment] → Functions → api/index.php
```
Sẽ hiển thị:
- ✅ Function invocation logs
- ✅ Request/response details
- ✅ Error messages

---

## 🎯 CÁC LỖI THƯỜNG GẶP VÀ CÁCH FIX

### 1️⃣ Lỗi: `No application encryption key has been specified`

**Logs sẽ hiển thị:**
```
RuntimeException: No application encryption key has been specified.
```

**Giải pháp:**
```bash
# Verify APP_KEY trong vercel.json
grep "APP_KEY" vercel.json

# Phải có dạng: base64:...
# Nếu không có, generate mới:
php artisan key:generate --show
```

---

### 2️⃣ Lỗi: `could not find driver (pdo_pgsql)`

**Logs sẽ hiển thị:**
```
PDOException: could not find driver
```

**Giải pháp:**
- Vercel PHP runtime mặc định có `pdo_pgsql`
- Nếu vẫn lỗi, kiểm tra `DB_CONNECTION=pgsql` trong vercel.json

---

### 3️⃣ Lỗi: `SQLSTATE[08006] connection failed`

**Logs sẽ hiển thị:**
```
SQLSTATE[08006]: Connection failure: 7 could not connect to server
```

**Giải pháp:**
```bash
# Kiểm tra DB credentials
grep "DB_" vercel.json

# Verify Supabase database đang chạy
curl -I https://db.zegdcaqmhvydyxgxrjvt.supabase.co

# Check password có đúng không (có ký tự đặc biệt !?)
```

---

### 4️⃣ Lỗi: `Class 'Illuminate\Foundation\Application' not found`

**Logs sẽ hiển thị:**
```
Fatal error: Class 'Illuminate\Foundation\Application' not found
```

**Giải pháp:**
```bash
# Composer dependencies chưa được cài
# Kiểm tra vercel-php runtime có chạy composer install không

# Workaround: Commit vendor folder (không khuyến khích)
git rm vendor --cached
git add vendor -f
git commit -m "Add vendor for Vercel"
git push
```

---

### 5️⃣ Lỗi: `require_once(/var/task/user/vendor/autoload.php): failed to open stream`

**Logs sẽ hiển thị:**
```
Warning: require_once(/var/task/user/vendor/autoload.php): 
failed to open stream: No such file or directory
```

**Giải pháp:**
- Vercel không tìm thấy vendor/autoload.php
- Cần đảm bảo composer.json và composer.lock được commit
- Vercel-php runtime sẽ tự động chạy composer install

---

### 6️⃣ Lỗi: `The stream or file "/tmp/storage/logs/laravel.log" could not be opened`

**Logs sẽ hiển thị:**
```
UnexpectedValueException: The stream or file "/tmp/storage/logs/laravel.log" 
could not be opened in append mode: failed to open stream
```

**Giải pháp:**
```json
// Đã fix trong vercel.json:
"LOG_CHANNEL": "stderr"  // ✅ Đúng - log ra stderr thay vì file
```

---

### 7️⃣ Lỗi: `View [welcome] not found`

**Logs sẽ hiển thị:**
```
InvalidArgumentException: View [welcome] not found.
```

**Giải pháp:**
```bash
# Kiểm tra VIEW_COMPILED_PATH
# Đã set trong vercel.json:
"VIEW_COMPILED_PATH": "/tmp/storage/framework/views"
```

---

## 🚀 SAU KHI XEM LOGS, HÃY:

### 1. Copy lỗi chính xác
Ví dụ:
```
[2026-07-14 02:04:00] production.ERROR: SQLSTATE[08006]: 
Connection failure: 7 could not connect to server: 
No such host is known
```

### 2. Share với tôi
Paste toàn bộ error message + stack trace vào chat

### 3. Tôi sẽ fix ngay
Với thông tin từ Runtime Logs, tôi sẽ biết chính xác:
- Thiếu env variable nào
- Config nào sai
- Code nào crash
- Dependency nào thiếu

---

## 📊 CHECKLIST ĐỂ DEBUG

Trước khi xem logs, kiểm tra nhanh:

```bash
# 1. Verify vercel.json có đầy đủ env variables
cd "/home/nvs1512/Project IT/San-tim-vien"
cat vercel.json | grep -E "(APP_KEY|DB_HOST|DB_PASSWORD)"

# 2. Verify api/index.php exists
ls -la api/index.php

# 3. Verify composer files committed
git ls-files | grep composer

# 4. Check latest commit
git log --oneline -1
```

---

## 🎯 KẾT LUẬN

**KHÔNG THỂ DEBUG mà không xem Runtime Logs!**

Trang lỗi 500 chỉ cho biết "có lỗi" nhưng không cho biết "lỗi gì".

👉 **Vui lòng vào Vercel Dashboard → Deployments → Click deployment mới nhất → Runtime Logs**

👉 **Copy toàn bộ error message và paste vào đây**

Tôi sẽ fix ngay khi có thông tin lỗi cụ thể! 🚀

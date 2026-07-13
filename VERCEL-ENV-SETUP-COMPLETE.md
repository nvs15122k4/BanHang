# 🔧 Hướng Dẫn Set Environment Variables Trên Vercel - ĐẦY ĐỦ

## ⚠️ LƯU Ý QUAN TRỌNG
Lỗi **FUNCTION_INVOCATION_FAILED** thường do **THIẾU hoặc SAI environment variables**.

## 🚀 Cách Set Environment Variables

### Bước 1: Vào Vercel Dashboard
1. Truy cập: https://vercel.com/dashboard
2. Chọn project: **BanHang** hoặc tên project của bạn
3. Vào tab **Settings** → **Environment Variables**

### Bước 2: Thêm TẤT CẢ các biến sau

**Copy từng biến và paste vào Vercel:**

---

#### 📌 CORE LARAVEL SETTINGS

```
Name: APP_NAME
Value: BanHang
Environment: Production, Preview, Development
```

```
Name: APP_ENV
Value: production
Environment: Production, Preview, Development
```

```
Name: APP_KEY
Value: base64:rC2RMoIEmkZ3Gz6lMAxA6f5iJFGK23l7rYeom+8nF88=
Environment: Production, Preview, Development
```

```
Name: APP_DEBUG
Value: false
Environment: Production
```

```
Name: APP_URL
Value: https://banhang.vnsang.io.vn
Environment: Production
```

---

#### 📌 DATABASE - SUPABASE

```
Name: DB_CONNECTION
Value: pgsql
Environment: Production, Preview, Development
```

```
Name: DB_HOST
Value: db.zegdcaqmhvydyxgxrjvt.supabase.co
Environment: Production, Preview, Development
```

```
Name: DB_PORT
Value: 5432
Environment: Production, Preview, Development
```

```
Name: DB_DATABASE
Value: postgres
Environment: Production, Preview, Development
```

```
Name: DB_USERNAME
Value: postgres
Environment: Production, Preview, Development
```

```
Name: DB_PASSWORD
Value: Kirishima084009!?
Environment: Production, Preview, Development
```

---

#### 📌 CACHE & SESSION

```
Name: CACHE_DRIVER
Value: array
Environment: Production, Preview, Development
```

```
Name: SESSION_DRIVER
Value: cookie
Environment: Production, Preview, Development
```

```
Name: SESSION_LIFETIME
Value: 120
Environment: Production, Preview, Development
```

```
Name: QUEUE_CONNECTION
Value: sync
Environment: Production, Preview, Development
```

```
Name: LOG_CHANNEL
Value: stderr
Environment: Production, Preview, Development
```

---

#### 📌 VERCEL SPECIFIC (Laravel Paths)

```
Name: VIEW_COMPILED_PATH
Value: /tmp/storage/framework/views
Environment: Production, Preview, Development
```

```
Name: CACHE_PATH
Value: /tmp/storage/framework/cache
Environment: Production, Preview, Development
```

---

#### 📌 CLOUDINARY (Upload Images)

```
Name: CLOUDINARY_URL
Value: ******dqfqgzrgx
Environment: Production, Preview, Development
```

```
Name: CLOUDINARY_CLOUD_NAME
Value: dqfqgzrgx
Environment: Production, Preview, Development
```

```
Name: CLOUDINARY_API_KEY
Value: 665672784584479
Environment: Production, Preview, Development
```

```
Name: CLOUDINARY_API_SECRET
Value: o_8bECMKYcugtFj5VKM8p3Cm2vQ
Environment: Production, Preview, Development
```

---

#### 📌 SUPABASE KEYS

```
Name: NEXT_PUBLIC_SUPABASE_URL
Value: https://zegdcaqmhvydyxgxrjvt.supabase.co
Environment: Production, Preview, Development
```

```
Name: NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY
Value: sb_publishable_f_X3W93mF48Y183YDKx3_g_WFD5LjUv
Environment: Production, Preview, Development
```

---

## 🔥 CÁCH NHANH: Dùng Vercel CLI

### Cài Vercel CLI

```bash
npm install -g vercel
vercel login
```

### Link project

```bash
cd "/home/nvs1512/Project IT/San-tim-vien"
vercel link
```

### Thêm tất cả env cùng lúc

```bash
# Thêm từng biến
vercel env add APP_NAME production
vercel env add APP_ENV production
vercel env add APP_KEY production
vercel env add APP_DEBUG production
vercel env add APP_URL production
vercel env add DB_CONNECTION production
vercel env add DB_HOST production
vercel env add DB_PORT production
vercel env add DB_DATABASE production
vercel env add DB_USERNAME production
vercel env add DB_PASSWORD production
vercel env add CACHE_DRIVER production
vercel env add SESSION_DRIVER production
vercel env add SESSION_LIFETIME production
vercel env add QUEUE_CONNECTION production
vercel env add LOG_CHANNEL production
vercel env add VIEW_COMPILED_PATH production
vercel env add CACHE_PATH production
vercel env add CLOUDINARY_URL production
vercel env add CLOUDINARY_CLOUD_NAME production
vercel env add CLOUDINARY_API_KEY production
vercel env add CLOUDINARY_API_SECRET production
vercel env add NEXT_PUBLIC_SUPABASE_URL production
vercel env add NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY production
```

Hoặc import từ file:

```bash
# Pull từ vercel.env
vercel env pull .env.production

# Hoặc import thủ công
cat vercel.env | while read line; do
  if [[ $line =~ ^[A-Z] ]]; then
    echo "$line"
  fi
done
```

---

## 🔍 Kiểm Tra Sau Khi Set

### 1. Kiểm tra trên Vercel Dashboard
- Vào **Settings** → **Environment Variables**
- Đảm bảo có **24 biến** (Production)

### 2. Redeploy
```bash
# Push commit rỗng để trigger redeploy
git commit --allow-empty -m "Redeploy after env vars setup"
git push
```

Hoặc trên Vercel Dashboard:
- Vào tab **Deployments**
- Click **...** ở deployment mới nhất
- Chọn **Redeploy**

### 3. Xem Logs
- Vào **Deployments** → Click vào deployment mới nhất
- Chọn **Runtime Logs** để xem lỗi chi tiết

---

## ❓ Troubleshooting

### Lỗi: "APP_KEY is not set"
```bash
# Generate key mới
php artisan key:generate --show

# Copy output và set vào Vercel
vercel env add APP_KEY production
# Paste: base64:...
```

### Lỗi: "Database connection failed"
Kiểm tra:
1. DB_HOST có đúng không
2. DB_PASSWORD có đúng không (có ký tự đặc biệt `!?`)
3. Supabase database có đang chạy không

### Lỗi: "Class not found"
```bash
# Đảm bảo composer.json và composer.lock đã commit
git add composer.json composer.lock
git commit -m "Ensure composer files committed"
git push
```

---

## 📊 Checklist Hoàn Thành

- [ ] Đã set tất cả 24 biến environment trên Vercel
- [ ] APP_URL = `https://banhang.vnsang.io.vn`
- [ ] APP_DEBUG = `false`
- [ ] DB credentials đúng
- [ ] Đã redeploy sau khi set env
- [ ] Site chạy bình thường (không còn 500 error)

---

## 🎯 Kết Quả Mong Đợi

Sau khi set đầy đủ environment variables và redeploy:
- ✅ Site load được tại: https://banhang.vnsang.io.vn
- ✅ Không còn lỗi 500 FUNCTION_INVOCATION_FAILED
- ✅ Database kết nối thành công
- ✅ Images upload qua Cloudinary

---

**Chúc may mắn! 🚀**

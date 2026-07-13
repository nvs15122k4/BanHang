# 🔐 Environment Variables for Vercel

## 📋 Required Variables - Copy vào Vercel Dashboard

Vào **Vercel Dashboard** → Project Settings → Environment Variables → Add New

### ✅ Core Laravel Variables

```bash
APP_NAME=BanHang
APP_ENV=production
APP_KEY=base64:rC2RMoIEmkZ3Gz6lMAxA6f5iJFGK23l7rYeom+8nF88=
APP_DEBUG=false
APP_URL=https://your-vercel-domain.vercel.app
```

> ⚠️ **Sau khi deploy xong, update APP_URL với domain thực của Vercel!**

---

### 🗄️ Database (Supabase PostgreSQL)

```bash
DB_CONNECTION=pgsql
DB_HOST=db.zegdcaqmhvydyxgxrjvt.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=Kirishima084009!?
```

---

### 💾 Cache & Session (Vercel Serverless Compatible)

```bash
CACHE_STORE=array
SESSION_DRIVER=cookie
QUEUE_CONNECTION=sync
LOG_CHANNEL=stderr
```

> ⚠️ **Không dùng `database` cho cache/session trên serverless!**
> - `CACHE_STORE=array` → In-memory cache (tốt nhất cho serverless)
> - `SESSION_DRIVER=cookie` → Session qua cookie, không cần storage
> - `LOG_CHANNEL=stderr` → Logs vào Vercel logs

---

### ☁️ Cloudinary (Image Upload)

```bash
CLOUDINARY_URL=cloudinary://665672784584479:o_8bECMKYcugtFj5VKM8p3Cm2vQ@dqfqgzrgx
CLOUDINARY_CLOUD_NAME=dqfqgzrgx
CLOUDINARY_API_KEY=665672784584479
CLOUDINARY_API_SECRET=o_8bECMKYcugtFj5VKM8p3Cm2vQ
```

---

### 💳 VietQR (Payment)

```bash
VIETQR_ACCOUNT_NO=1014232408
VIETQR_ACCOUNT_NAME="Nguyen Van Sang"
VIETQR_BIN=970436
VIETQR_TEMPLATE=compact2
```

> Optional: Thêm VIETQR_CLIENT_ID và VIETQR_API_KEY nếu có

---

### 🗄️ Supabase (Optional - nếu dùng Supabase SDK)

```bash
NEXT_PUBLIC_SUPABASE_URL=https://zegdcaqmhvydyxgxrjvt.supabase.co
NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY=sb_publishable_f_X3W93mF48Y183YDKx3_g_WFD5LjUv
```

---

## 🚀 Cách Add vào Vercel

### Method 1: Vercel Dashboard (Recommended)

1. Vào https://vercel.com/[username]/san-tim-vien/settings/environment-variables
2. Click **Add New**
3. Nhập:
   - **Key**: APP_NAME
   - **Value**: BanHang
   - **Environment**: Production, Preview, Development (tick all)
4. Click **Save**
5. Lặp lại cho tất cả variables ở trên

### Method 2: Vercel CLI (Nhanh hơn)

```bash
# Install Vercel CLI
npm i -g vercel

# Login
vercel login

# Link project
vercel link

# Add từng variable
vercel env add APP_NAME production
vercel env add APP_ENV production
vercel env add APP_KEY production
# ... (repeat for all)

# Pull về local để test
vercel env pull
```

---

## ⚠️ Critical Notes

### 🔴 Khác biệt giữa Local và Production:

| Variable | Local (.env) | Production (Vercel) |
|----------|-------------|---------------------|
| APP_ENV | `local` | `production` |
| APP_DEBUG | `true` | `false` |
| APP_URL | `http://127.0.0.1:8000` | `https://[domain].vercel.app` |
| CACHE_STORE | `database` | `array` |
| SESSION_DRIVER | `database` | `cookie` |
| LOG_CHANNEL | `stack` | `stderr` |

### ⚡ Tại sao phải đổi?

**Serverless = Stateless:**
- Mỗi request chạy trên container khác nhau
- Không có persistent filesystem
- `/tmp` là ephemeral (tạm thời)

**Solutions:**
- Cache → `array` (in-memory, fast)
- Session → `cookie` (client-side)
- Queue → `sync` (hoặc external: Redis, SQS)
- Logs → `stderr` (Vercel capture logs)

---

## 📝 Quick Copy-Paste

```bash
APP_NAME=BanHang
APP_ENV=production
APP_KEY=base64:rC2RMoIEmkZ3Gz6lMAxA6f5iJFGK23l7rYeom+8nF88=
APP_DEBUG=false
APP_URL=https://your-domain.vercel.app

DB_CONNECTION=pgsql
DB_HOST=db.zegdcaqmhvydyxgxrjvt.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=Kirishima084009!?

CACHE_STORE=array
SESSION_DRIVER=cookie
QUEUE_CONNECTION=sync
LOG_CHANNEL=stderr

CLOUDINARY_URL=cloudinary://665672784584479:o_8bECMKYcugtFj5VKM8p3Cm2vQ@dqfqgzrgx
CLOUDINARY_CLOUD_NAME=dqfqgzrgx
CLOUDINARY_API_KEY=665672784584479
CLOUDINARY_API_SECRET=o_8bECMKYcugtFj5VKM8p3Cm2vQ

VIETQR_ACCOUNT_NO=1014232408
VIETQR_ACCOUNT_NAME="Nguyen Van Sang"
VIETQR_BIN=970436
VIETQR_TEMPLATE=compact2
```

---

## ✅ After Adding Variables

1. **Redeploy** → Vercel tự động redeploy khi add env
2. **Update APP_URL** → Sau khi có domain, update lại APP_URL
3. **Test** → Visit `https://[domain].vercel.app`

---

## 🐛 Troubleshooting

**Lỗi 500 sau khi add env?**
- Check Vercel Function Logs: https://vercel.com/[username]/[project]/logs
- Verify APP_KEY có đúng format: `base64:...`
- Check DB credentials (test connect bằng psql)

**Session không work?**
- Verify `SESSION_DRIVER=cookie` (NOT database)
- Check APP_KEY đã set (required for cookie encryption)

**Cache không work?**
- Verify `CACHE_STORE=array`
- Serverless không support `file` hoặc `database` cache

---

🎉 **Deploy thành công? Update APP_URL và enjoy!**

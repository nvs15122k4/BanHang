# 🚀 Hướng Dẫn Deploy Laravel Lên Vercel

## 📋 Mục Lục
1. [Chuẩn Bị](#1-chuẩn-bị)
2. [Cài Đặt Vercel CLI](#2-cài-đặt-vercel-cli)
3. [Push Code Lên GitHub](#3-push-code-lên-github)
4. [Deploy Qua Vercel Dashboard](#4-deploy-qua-vercel-dashboard)
5. [Deploy Qua Vercel CLI](#5-deploy-qua-vercel-cli)
6. [Cấu Hình Environment Variables](#6-cấu-hình-environment-variables)
7. [Custom Domain](#7-custom-domain)
8. [Troubleshooting](#8-troubleshooting)

---

## 1. Chuẩn Bị

### ✅ Checklist Trước Khi Deploy

- [x] Database đã được setup trên Supabase
- [x] Git repository đã sẵn sàng
- [x] `vercel.json` đã được tạo
- [x] `api/index.php` đã được tạo
- [ ] Tài khoản Vercel (đăng ký tại https://vercel.com)
- [ ] Code đã được push lên GitHub

### 📝 Files Quan Trọng Đã Tạo

```
BanHang/
├── vercel.json          # Vercel configuration
├── api/
│   └── index.php       # Serverless function entry point
├── build.sh            # Build script
├── .env.example        # Environment template
└── public/             # Static assets
```

---

## 2. Cài Đặt Vercel CLI

### Cài Đặt

```bash
# Cài Vercel CLI globally
npm install -g vercel

# Hoặc dùng npx (không cần cài)
npx vercel --version
```

### Đăng Nhập

```bash
# Đăng nhập vào Vercel
vercel login

# Chọn phương thức: GitHub, GitLab, Bitbucket, hoặc Email
```

---

## 3. Push Code Lên GitHub

### Kiểm Tra Git Status

```bash
cd /home/nvs1512/Project\ IT/San-tim-vien/BanHang

# Check current branch
git branch

# Check status
git status
```

### Add Files Mới

```bash
# Add vercel config files
git add vercel.json api/index.php build.sh

# Add migration files
git add SUPABASE-MIGRATION-SUCCESS.md migrate_data.php

# Add all other changes
git add .
```

### Commit Changes

```bash
git commit -m "feat: Add Vercel deployment configuration

- Added vercel.json for Vercel configuration
- Created api/index.php as serverless entry point
- Added build.sh for deployment optimization
- Migrated database to Supabase PostgreSQL
- Updated environment configuration for production

Co-authored-by: Copilot <223556219+Copilot@users.noreply.github.com>"
```

### Push Lên GitHub

```bash
# Push to current branch (testing)
git push origin testing

# Hoặc merge vào main và push
git checkout main
git merge testing
git push origin main
```

---

## 4. Deploy Qua Vercel Dashboard (Khuyến Nghị)

### Bước 1: Truy Cập Vercel

1. Đi tới: https://vercel.com/new
2. Đăng nhập bằng GitHub account

### Bước 2: Import Repository

1. Click **"Import Project"** hoặc **"Add New..."** → **"Project"**
2. Chọn **"Import Git Repository"**
3. Authorize Vercel access GitHub
4. Tìm và chọn repository: `nvs15122k4/BanHang`
5. Click **"Import"**

### Bước 3: Configure Project

#### Framework Preset
- **Framework**: Other (hoặc để trống)

#### Build & Output Settings
- **Build Command**: `./build.sh`
- **Output Directory**: `public`
- **Install Command**: `npm install`

#### Root Directory
- Leave as `.` (root)

### Bước 4: Environment Variables

Click **"Environment Variables"** và thêm các biến sau:

#### 🔑 Laravel Core
```env
APP_NAME=BanHang
APP_ENV=production
APP_KEY=base64:rC2RMoIEmkZ3Gz6lMAxA6f5iJFGK23l7rYeom+8nF88=
APP_DEBUG=false
APP_URL=https://your-project.vercel.app
```

#### 🗄️ Database (Supabase)
```env
DB_CONNECTION=pgsql
DB_HOST=db.zegdcaqmhvydyxgxrjvt.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=Kirishima084009!?
```

#### 📦 Cache & Session
```env
CACHE_DRIVER=array
SESSION_DRIVER=cookie
SESSION_LIFETIME=120
QUEUE_CONNECTION=sync
```

#### ☁️ Supabase Keys
```env
NEXT_PUBLIC_SUPABASE_URL=https://zegdcaqmhvydyxgxrjvt.supabase.co
NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY=sb_publishable_f_X3W93mF48Y183YDKx3_g_WFD5LjUv
```

#### 📸 Cloudinary (Optional)
```env
CLOUDINARY_URL=******dqfqgzrgx
CLOUDINARY_CLOUD_NAME=dqfqgzrgx
CLOUDINARY_API_KEY=665672784584479
CLOUDINARY_API_SECRET=o_8bECMKYcugtFj5VKM8p3Cm2vQ
```

**💡 Tip**: Để add nhiều variables nhanh, paste tất cả vào text area!

### Bước 5: Deploy

1. Click **"Deploy"**
2. Đợi build process (2-5 phút)
3. Khi hoàn thành, click vào URL để xem site

---

## 5. Deploy Qua Vercel CLI (Alternative)

### Bước 1: Link Project

```bash
cd /home/nvs1512/Project\ IT/San-tim-vien/BanHang

# Initialize Vercel project
vercel

# Trả lời các câu hỏi:
# ? Set up and deploy? Yes
# ? Which scope? (your-username)
# ? Link to existing project? No
# ? What's your project's name? banhang-online
# ? In which directory is your code located? ./
```

### Bước 2: Add Environment Variables

**Tạo file để bulk add:**

```bash
cat > .env.vercel << 'EOF'
APP_NAME=BanHang
APP_ENV=production
APP_KEY=base64:rC2RMoIEmkZ3Gz6lMAxA6f5iJFGK23l7rYeom+8nF88=
APP_DEBUG=false
APP_URL=https://banhang-online.vercel.app
DB_CONNECTION=pgsql
DB_HOST=db.zegdcaqmhvydyxgxrjvt.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=Kirishima084009!?
CACHE_DRIVER=array
SESSION_DRIVER=cookie
QUEUE_CONNECTION=sync
NEXT_PUBLIC_SUPABASE_URL=https://zegdcaqmhvydyxgxrjvt.supabase.co
NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY=sb_publishable_f_X3W93mF48Y183YDKx3_g_WFD5LjUv
EOF
```

**Add từng variable:**

```bash
# Hoặc add từng cái
vercel env add APP_NAME
vercel env add APP_ENV
vercel env add APP_KEY
# ... etc

# Pull về để test local
vercel env pull .env.production
```

### Bước 3: Deploy to Production

```bash
# Deploy to production
vercel --prod

# Output sẽ có URL: https://banhang-online.vercel.app
```

---

## 6. Cấu Hình Environment Variables

### Add Qua CLI

```bash
# Add một variable (interactive)
vercel env add APP_KEY

# Add cho environment cụ thể
vercel env add APP_KEY production

# Pull tất cả về local
vercel env pull
```

### Add Qua Dashboard

1. Vào project settings: https://vercel.com/your-username/banhang-online/settings/environment-variables
2. Click **"Add New"**
3. Paste key-value pair
4. Chọn environments: Production, Preview, Development
5. Click **"Save"**

### 🔒 Secret Variables

Với các sensitive data như passwords:

```bash
# Add as secret (CLI)
echo "Kirishima084009!?" | vercel secrets add db-password

# Sau đó reference trong env vars
# DB_PASSWORD=@db-password
```

---

## 7. Custom Domain

### Option 1: Qua Dashboard

1. Vào: Project Settings → Domains
2. Click **"Add"**
3. Nhập domain: `yourdomain.com`
4. Configure DNS records:

```
Type: A
Name: @
Value: 76.76.21.21

Type: CNAME
Name: www
Value: cname.vercel-dns.com
```

### Option 2: Qua CLI

```bash
# Add domain
vercel domains add yourdomain.com

# Check status
vercel domains inspect yourdomain.com

# List all domains
vercel domains ls
```

---

## 8. Troubleshooting

### ❌ Build Failed

**Lỗi: "composer: command not found"**

```bash
# Solution: Thêm vào vercel.json
{
  "functions": {
    "api/*.php": {
      "runtime": "vercel-php@0.6.0"
    }
  }
}
```

**Lỗi: "npm run build failed"**

```bash
# Check package.json có script "build"
# Thêm vào nếu chưa có:
{
  "scripts": {
    "build": "vite build"
  }
}
```

### ❌ Database Connection Failed

**Check environment variables:**

```bash
# Via CLI
vercel env ls

# Test connection locally
php artisan tinker --execute 'DB::connection()->getPdo();'
```

**Verify database credentials:**
1. Go to Supabase Dashboard
2. Settings → Database
3. Confirm host, port, password

### ❌ 500 Internal Server Error

**Check logs:**

```bash
# Via CLI
vercel logs

# Or in dashboard
# Project → Deployments → Click deployment → Logs
```

**Common fixes:**

```bash
# Clear Laravel cache
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Regenerate optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### ❌ Assets Not Loading (404)

**Check routes in vercel.json:**

```json
{
  "routes": [
    {
      "src": "/build/(.*)",
      "dest": "/build/$1"
    },
    {
      "src": "/(css|js|images)/(.*)",
      "dest": "/$1/$2"
    }
  ]
}
```

**Verify build output:**

```bash
# Check public/build/ exists after build
ls -la public/build/

# Check manifest.json
cat public/build/manifest.json
```

### ❌ Session Not Working

**Use cookie-based sessions:**

```env
SESSION_DRIVER=cookie
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

### 🔍 Debug Mode

**Enable temporarily:**

```bash
# Add to environment variables
APP_DEBUG=true
LOG_LEVEL=debug

# Deploy and check logs
vercel --prod
vercel logs --follow
```

**⚠️ Remember to disable after debugging!**

---

## 🎯 Quick Deploy Script

**Tạo script tự động hóa:**

```bash
cat > deploy_vercel.sh << 'DEPLOY'
#!/bin/bash
set -e

echo "🚀 Starting Vercel deployment..."

# 1. Git commit and push
echo "📦 Committing changes..."
git add .
git commit -m "deploy: Update for Vercel deployment" || true
git push origin main

# 2. Deploy to Vercel
echo "🌐 Deploying to Vercel..."
vercel --prod

echo "✅ Deployment complete!"
echo "Check your site at: https://banhang-online.vercel.app"
DEPLOY

chmod +x deploy_vercel.sh
```

**Chạy:**
```bash
./deploy_vercel.sh
```

---

## 📊 Monitoring & Analytics

### View Deployment Status

```bash
# List all deployments
vercel ls

# Get deployment info
vercel inspect [deployment-url]

# View logs
vercel logs [deployment-url]
```

### Enable Vercel Analytics

```bash
# Install
npm install @vercel/analytics

# Add to layout
# resources/views/layouts/app.blade.php
```

```html
<script type="module">
  import { inject } from 'https://cdn.jsdelivr.net/npm/@vercel/analytics@1/dist/index.js'
  inject()
</script>
```

---

## ✅ Post-Deployment Checklist

- [ ] Site accessible at Vercel URL
- [ ] Database connection working
- [ ] Assets loading correctly (CSS, JS, images)
- [ ] Authentication working
- [ ] Forms submitting properly
- [ ] All routes accessible
- [ ] SSL/HTTPS enabled
- [ ] Custom domain configured (if applicable)
- [ ] Environment variables set correctly
- [ ] Monitoring/analytics enabled

---

## 🔗 Useful Links

- **Vercel Dashboard**: https://vercel.com/dashboard
- **Project Settings**: https://vercel.com/your-username/banhang-online/settings
- **Domains**: https://vercel.com/your-username/banhang-online/settings/domains
- **Environment Variables**: https://vercel.com/your-username/banhang-online/settings/environment-variables
- **Deployments**: https://vercel.com/your-username/banhang-online/deployments
- **Vercel Docs**: https://vercel.com/docs
- **Vercel PHP Runtime**: https://github.com/vercel-community/php

---

## 📞 Support

Gặp vấn đề? Check:
1. ✅ Vercel logs: `vercel logs`
2. ✅ Build logs trong dashboard
3. ✅ Laravel logs: `storage/logs/laravel.log`
4. ✅ Supabase status: https://status.supabase.com

---

**🎉 Chúc mừng! Bạn đã deploy Laravel app lên Vercel thành công!**

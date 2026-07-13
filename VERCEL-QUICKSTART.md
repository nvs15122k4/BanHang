# 🚀 Quick Start: Deploy to Vercel

## ⚡ Tóm Tắt Nhanh (5 phút)

### Bước 1: Push Code Lên GitHub
```bash
cd /home/nvs1512/Project\ IT/San-tim-vien/BanHang

# Add files
git add .

# Commit
git commit -m "deploy: Add Vercel configuration"

# Push
git push origin main
```

### Bước 2: Deploy Qua Vercel Dashboard

1. **Truy cập**: https://vercel.com/new
2. **Import**: `nvs15122k4/BanHang`
3. **Configure Project Settings**:
   - **Root Directory**: `BanHang` ⚠️ **BẮT BUỘC!**
   - **Framework Preset**: `Other`
   - **Build Command**: **(Để trống - sử dụng vercel.json)**
   - **Output Directory**: **(Để trống)**
   - **Install Command**: **(Để trống - sử dụng vercel.json)**

4. **Environment Variables** (Copy-paste tất cả):
```env
APP_NAME=BanHang
APP_ENV=production
APP_KEY=base64:rC2RMoIEmkZ3Gz6lMAxA6f5iJFGK23l7rYeom+8nF88=
APP_DEBUG=false
APP_URL=https://banhang.vnsang.io.vn
DB_CONNECTION=pgsql
DB_HOST=db.zegdcaqmhvydyxgxrjvt.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=Kirishima084009!?
CACHE_DRIVER=array
SESSION_DRIVER=cookie
SESSION_LIFETIME=120
QUEUE_CONNECTION=sync
NEXT_PUBLIC_SUPABASE_URL=https://zegdcaqmhvydyxgxrjvt.supabase.co
NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY=sb_publishable_f_X3W93mF48Y183YDKx3_g_WFD5LjUv
CLOUDINARY_URL=******dqfqgzrgx
CLOUDINARY_CLOUD_NAME=dqfqgzrgx
CLOUDINARY_API_KEY=665672784584479
CLOUDINARY_API_SECRET=o_8bECMKYcugtFj5VKM8p3Cm2vQ
```

5. **Click Deploy!** 🚀

---

## 🎯 Hoặc Sử Dụng Script Tự Động

```bash
# Chạy script deploy tự động
./deploy_vercel.sh
```

Script sẽ:
- ✅ Commit và push code
- ✅ Hướng dẫn deploy qua dashboard
- ✅ Hoặc deploy trực tiếp qua CLI

---

## 📁 Files Đã Tạo

- ✅ `vercel.json` - Vercel configuration
- ✅ `api/index.php` - Serverless entry point  
- ✅ `build.sh` - Build script
- ✅ `.vercelignore` - Files to ignore
- ✅ `deploy_vercel.sh` - Automated deploy script
- ✅ `VERCEL-DEPLOYMENT-GUIDE.md` - Full documentation

---

## 🔍 Kiểm Tra Sau Deploy

### Test Site

```bash
# Truy cập URL Vercel của bạn
curl https://your-project.vercel.app

# Hoặc mở browser
xdg-open https://your-project.vercel.app
```

### Check Logs

```bash
# Via CLI
vercel logs

# Hoặc Dashboard
# https://vercel.com/dashboard → Deployments → Logs
```

### Test Database Connection

```bash
# Local test với production config
php artisan tinker --execute 'DB::connection()->getPdo(); echo "✅ Connected!\n";'
```

---

## ❓ Troubleshooting

### Build Failed?

```bash
# Check build logs in Vercel dashboard
# Common issues:
# 1. Missing composer dependencies
# 2. npm build errors
# 3. Permission issues

# Fix: Update build.sh or vercel.json
```

### 500 Error?

```bash
# Check environment variables
# Make sure APP_KEY is set correctly
# Verify database credentials

# Enable debug temporarily
APP_DEBUG=true
```

### Assets Not Loading?

```bash
# Check routes in vercel.json
# Verify public/build/ directory exists
# Run: npm run build locally to test
```

---

## 📊 Monitoring

### View All Deployments

```bash
vercel ls
```

### Inspect Specific Deployment

```bash
vercel inspect [deployment-url]
```

### Real-time Logs

```bash
vercel logs --follow
```

---

## 🔄 Update Deployment

### Push New Changes

```bash
# Make changes
git add .
git commit -m "update: Your changes"
git push origin main

# Vercel auto-deploys on push!
```

### Manual Redeploy

```bash
vercel --prod
```

---

## 🎉 Success Checklist

- [ ] Code pushed to GitHub
- [ ] Vercel project created
- [ ] Environment variables set
- [ ] Build successful
- [ ] Site accessible
- [ ] Database working
- [ ] Assets loading
- [ ] No console errors

---

## 📚 Documentation

- **Full Guide**: [VERCEL-DEPLOYMENT-GUIDE.md](VERCEL-DEPLOYMENT-GUIDE.md)
- **Supabase Setup**: [SUPABASE-MIGRATION-SUCCESS.md](SUPABASE-MIGRATION-SUCCESS.md)
- **Full Deployment Guide**: [DEPLOYMENT-GUIDE.md](../DEPLOYMENT-GUIDE.md)

---

## 🆘 Need Help?

- Vercel Docs: https://vercel.com/docs
- Supabase Docs: https://supabase.com/docs
- Laravel Docs: https://laravel.com/docs

---

**Ready to deploy? Run:**
```bash
./deploy_vercel.sh
```

**Or follow the guide:**
```bash
cat VERCEL-DEPLOYMENT-GUIDE.md
```

🚀 **Good luck!**

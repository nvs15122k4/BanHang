# ⚡ Vercel Quick Setup Guide

## 🎯 Bước 1: Add Environment Variables

**Vào Vercel Dashboard:**
https://vercel.com/[username]/san-tim-vien/settings/environment-variables

**Click "Add New" và paste từng dòng từ file `vercel.env`:**

```bash
APP_NAME=BanHang
APP_ENV=production
APP_KEY=base64:rC2RMoIEmkZ3Gz6lMAxA6f5iJFGK23l7rYeom+8nF88=
APP_DEBUG=false
APP_URL=https://your-app.vercel.app

DB_CONNECTION=pgsql
DB_HOST=db.zegdcaqmhvydyxgxrjvt.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=Kirishima084009!?

CACHE_DRIVER=array
SESSION_DRIVER=cookie
QUEUE_CONNECTION=sync
LOG_CHANNEL=stderr

CLOUDINARY_URL=cloudinary://665672784584479:o_8bECMKYcugtFj5VKM8p3Cm2vQ@dqfqgzrgx
CLOUDINARY_CLOUD_NAME=dqfqgzrgx
CLOUDINARY_API_KEY=665672784584479
CLOUDINARY_API_SECRET=o_8bECMKYcugtFj5VKM8p3Cm2vQ
```

**Lưu ý:**
- Tick **Production, Preview, Development** cho mỗi variable
- Sau khi add xong, Vercel tự động **Redeploy**

---

## 🚀 Bước 2: Đợi Deploy Hoàn Thành

- Vercel Dashboard → Deployments → Xem status
- Đợi **2-3 phút** cho build + deploy
- ✅ Status: **Ready** → Deployment thành công!

---

## 🔧 Bước 3: Update APP_URL (Sau Deploy)

1. Copy URL từ Vercel (vd: `https://san-tim-vien-abc123.vercel.app`)
2. Quay lại Environment Variables
3. Edit `APP_URL` → Paste URL vừa copy
4. Save → Vercel sẽ redeploy lại

---

## ✅ Done!

Visit: `https://[your-domain].vercel.app`

**Nếu gặp lỗi:**
- Xem logs: Vercel Dashboard → Deployments → [latest] → Function Logs
- Check env variables: Verify tất cả đã add đúng
- Redeploy manual: Click **Redeploy** button

---

## 📝 Files Reference

- `vercel.env` - Template environment variables
- `vercel.json` - Vercel configuration (đã setup sẵn)
- `VERCEL-ENV-VARIABLES.md` - Chi tiết về từng biến

---

**🎉 Happy Deploying!**

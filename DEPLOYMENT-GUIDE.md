# 🚀 Hướng Dẫn Deploy Toàn Diện

> **Hướng dẫn chi tiết từng bước để deploy dự án lên GitHub, Supabase và Vercel**

---

## 📋 Mục Lục

1. [Chuẩn Bị](#-1-chuẩn-bị)
2. [Upload Code Lên GitHub](#-2-upload-code-lên-github)
3. [Setup Database Trên Supabase](#-3-setup-database-trên-supabase)
4. [Deploy Web Lên Vercel](#-4-deploy-web-lên-vercel)
5. [Tự Động Hóa CI/CD](#-5-tự-động-hóa-cicd)
6. [Troubleshooting](#-6-troubleshooting)

---

## 🔧 1. Chuẩn Bị

### 1.1 Kiểm Tra Yêu Cầu Hệ Thống

```bash
# Kiểm tra Git
git --version

# Kiểm tra Node.js (cần >= 18)
node --version

# Kiểm tra Composer (cho Laravel)
composer --version

# Kiểm tra PHP (cần >= 8.1)
php --version
```

### 1.2 Tạo Tài Khoản (Nếu Chưa Có)

- ✅ GitHub: https://github.com/signup
- ✅ Supabase: https://supabase.com
- ✅ Vercel: https://vercel.com/signup

### 1.3 Cài Đặt CLI Tools

```bash
# Cài Vercel CLI
npm install -g vercel

# Cài Supabase CLI
npm install -g supabase

# Đăng nhập Vercel
vercel login

# Đăng nhập Supabase
supabase login
```

---

## 📦 2. Upload Code Lên GitHub

### 2.1 Khởi Tạo Git Repository (Nếu Chưa Có)

```bash
cd /home/nvs1512/Project\ IT/San-tim-vien/BanHang

# Kiểm tra xem đã có git chưa
git status

# Nếu chưa có, khởi tạo
git init
```

### 2.2 Tạo File .gitignore (Quan Trọng!)

```bash
cat > .gitignore << 'EOF'
# Laravel
/vendor/
/node_modules/
/public/hot
/public/storage
/storage/*.key
.env
.env.backup
.phpunit.result.cache
Homestead.json
Homestead.yaml
npm-debug.log
yarn-error.log

# Vite
/public/build/

# IDE
.idea/
.vscode/
*.swp
*.swo
*~

# OS
.DS_Store
Thumbs.db

# Testing
/test-results/
/playwright-report/
/playwright/.cache/

# Composer
composer.phar
composer.lock

# Logs
*.log
EOF
```

### 2.3 Tạo .env.example (Template Cho Production)

```bash
cp .env .env.example

# Xóa các giá trị nhạy cảm trong .env.example
nano .env.example
```

**Nội dung .env.example nên có:**

```env
APP_NAME=Laravel
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.vercel.app

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=your-supabase-project.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

### 2.4 Commit Code Lần Đầu

```bash
# Thêm tất cả files
git add .

# Commit
git commit -m "Initial commit: Setup Laravel project

- Added Laravel base structure
- Configured Vite for frontend
- Added Playwright E2E tests
- Ready for deployment

Co-authored-by: Copilot <223556219+Copilot@users.noreply.github.com>"

# Xem trạng thái
git status
```

### 2.5 Tạo Repository Trên GitHub

**Cách 1: Qua GitHub CLI (Nhanh)**

```bash
# Cài GitHub CLI nếu chưa có
# Ubuntu/Debian
sudo apt install gh

# Đăng nhập
gh auth login

# Tạo repo và push
gh repo create ban-hang-online --public --source=. --push

# Hoặc private
gh repo create ban-hang-online --private --source=. --push
```

**Cách 2: Qua Web Interface**

1. Truy cập https://github.com/new
2. Đặt tên repo: `ban-hang-online`
3. Chọn Public hoặc Private
4. **KHÔNG** chọn "Initialize with README"
5. Click "Create repository"

```bash
# Link repo với GitHub
git remote add origin https://github.com/YOUR-USERNAME/ban-hang-online.git

# Push code
git branch -M main
git push -u origin main
```

### 2.6 Tạo Branches Cho Development

```bash
# Tạo branch develop
git checkout -b develop
git push -u origin develop

# Tạo branch staging
git checkout -b staging
git push -u origin staging

# Về lại main
git checkout main
```

### 2.7 Setup Branch Protection (Khuyến Nghị)

Trên GitHub Repository:
1. Settings → Branches → Add rule
2. Branch name pattern: `main`
3. ✅ Require pull request reviews before merging
4. ✅ Require status checks to pass before merging
5. Save changes

---

## 🗄️ 3. Setup Database Trên Supabase

### 3.1 Tạo Project Mới

```bash
# Qua CLI
supabase projects create ban-hang-online --org-id YOUR_ORG_ID --db-password YOUR_STRONG_PASSWORD --region southeast-asia

# Hoặc qua Web: https://app.supabase.com/projects
```

**Qua Web Interface:**
1. Truy cập https://app.supabase.com
2. Click "New Project"
3. Chọn Organization
4. Điền thông tin:
   - **Name**: Ban Hang Online
   - **Database Password**: (tạo password mạnh)
   - **Region**: Southeast Asia (Singapore)
5. Click "Create new project"

### 3.2 Lấy Thông Tin Kết Nối

```bash
# Lấy connection string
supabase projects list

# Hoặc qua Web: Project Settings → Database → Connection string
```

**Connection Info:**
- Host: `db.YOUR_PROJECT_REF.supabase.co`
- Port: `5432`
- Database: `postgres`
- User: `postgres`
- Password: (password bạn đã tạo)

### 3.3 Export Database Schema Từ Local

```bash
cd /home/nvs1512/Project\ IT/San-tim-vien/BanHang

# Kiểm tra migrations
ls -la database/migrations/

# Export schema từ MySQL local (nếu đang dùng)
php artisan schema:dump

# Hoặc export trực tiếp từ MySQL
mysqldump -u root -p --no-data --skip-triggers banhang_db > schema.sql
```

### 3.4 Chuyển Đổi Sang PostgreSQL

**Tạo file migration converter:**

```bash
cat > convert_to_postgres.sh << 'EOF'
#!/bin/bash

# Chuyển đổi MySQL dumps sang PostgreSQL
sed -i 's/ENGINE=InnoDB//' schema.sql
sed -i 's/AUTO_INCREMENT/SERIAL/' schema.sql
sed -i 's/UNSIGNED//g' schema.sql
sed -i 's/INT(11)/INTEGER/g' schema.sql
sed -i 's/DATETIME/TIMESTAMP/g' schema.sql
sed -i 's/`/"/g' schema.sql

echo "✅ Conversion complete!"
EOF

chmod +x convert_to_postgres.sh
./convert_to_postgres.sh
```

### 3.5 Chạy Migrations Trên Supabase

```bash
# Link project với Supabase CLI
supabase link --project-ref YOUR_PROJECT_REF

# Chạy migrations
supabase db push

# Hoặc dùng Laravel migrations trực tiếp
# Cập nhật .env với thông tin Supabase
DB_CONNECTION=pgsql
DB_HOST=db.YOUR_PROJECT_REF.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=YOUR_PASSWORD

# Chạy migrations
php artisan migrate --force

# Chạy seeders (nếu có)
php artisan db:seed --force
```

### 3.6 Setup Row Level Security (RLS)

**Tạo file SQL cho RLS:**

```sql
-- File: database/supabase/rls-policies.sql

-- Enable RLS on tables
ALTER TABLE users ENABLE ROW LEVEL SECURITY;
ALTER TABLE products ENABLE ROW LEVEL SECURITY;
ALTER TABLE orders ENABLE ROW LEVEL SECURITY;

-- Public read access for products
CREATE POLICY "Public products are viewable by everyone"
ON products FOR SELECT
USING (true);

-- Users can only see their own orders
CREATE POLICY "Users can view their own orders"
ON orders FOR SELECT
USING (auth.uid() = user_id);

-- Admin full access
CREATE POLICY "Admin has full access"
ON orders FOR ALL
USING (
  EXISTS (
    SELECT 1 FROM users
    WHERE users.id = auth.uid()
    AND users.role = 'admin'
  )
);
```

```bash
# Apply RLS policies
supabase db execute -f database/supabase/rls-policies.sql
```

### 3.7 Tạo API Keys

1. Truy cập: Project Settings → API
2. Copy các keys sau:
   - **Project URL**: `https://YOUR_PROJECT_REF.supabase.co`
   - **anon/public key**: (cho client-side)
   - **service_role key**: (cho server-side, giữ bí mật!)

### 3.8 Backup Automation (Khuyến Nghị)

```bash
# Tạo daily backup script
cat > backup_supabase.sh << 'EOF'
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="$HOME/backups/supabase"

mkdir -p $BACKUP_DIR

# Backup database
supabase db dump -f $BACKUP_DIR/backup_$DATE.sql

# Xóa backups cũ hơn 7 ngày
find $BACKUP_DIR -name "backup_*.sql" -mtime +7 -delete

echo "✅ Backup completed: backup_$DATE.sql"
EOF

chmod +x backup_supabase.sh

# Thêm vào crontab (chạy lúc 2AM hàng ngày)
crontab -e
# Thêm dòng:
# 0 2 * * * /home/nvs1512/Project\ IT/San-tim-vien/BanHang/backup_supabase.sh
```

---

## 🌐 4. Deploy Web Lên Vercel

### 4.1 Cấu Hình Project Cho Vercel

**Tạo file vercel.json:**

```bash
cd /home/nvs1512/Project\ IT/San-tim-vien/BanHang

cat > vercel.json << 'EOF'
{
  "version": 2,
  "framework": null,
  "builds": [
    {
      "src": "package.json",
      "use": "@vercel/static-build",
      "config": {
        "distDir": "public"
      }
    }
  ],
  "routes": [
    {
      "src": "/build/(.*)",
      "dest": "/build/$1"
    },
    {
      "src": "/css/(.*)",
      "dest": "/css/$1"
    },
    {
      "src": "/js/(.*)",
      "dest": "/js/$1"
    },
    {
      "src": "/images/(.*)",
      "dest": "/images/$1"
    },
    {
      "src": "/(.*)",
      "dest": "/index.php"
    }
  ],
  "env": {
    "APP_ENV": "production",
    "APP_DEBUG": "false",
    "LOG_CHANNEL": "stderr"
  },
  "build": {
    "env": {
      "PHP_VERSION": "8.2"
    }
  }
}
EOF
```

**Tạo file api/index.php (Vercel Serverless):**

```bash
mkdir -p api

cat > api/index.php << 'EOF'
<?php

// Forward Vercel requests to Laravel
require __DIR__ . '/../public/index.php';
EOF
```

**Cập nhật package.json với build script:**

```bash
cat > build.sh << 'EOF'
#!/bin/bash
set -e

echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "🔑 Generating APP_KEY..."
php artisan key:generate --force --no-interaction

echo "🏗️  Building assets..."
npm ci
npm run build

echo "🗄️  Running migrations..."
php artisan migrate --force --no-interaction

echo "📝 Optimizing..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Build complete!"
EOF

chmod +x build.sh
```

**Thêm vào package.json:**

```json
{
  "scripts": {
    "build": "vite build",
    "vercel-build": "./build.sh"
  }
}
```

### 4.2 Deploy Qua Vercel CLI

```bash
cd /home/nvs1512/Project\ IT/San-tim-vien/BanHang

# Deploy lần đầu (sẽ tạo project)
vercel

# Làm theo hướng dẫn:
# - Setup and deploy? Yes
# - Which scope? Chọn account của bạn
# - Link to existing project? No
# - Project name? ban-hang-online
# - In which directory is your code located? ./
# - Override settings? No

# Deploy production
vercel --prod
```

### 4.3 Deploy Qua Vercel Web Interface

1. Truy cập https://vercel.com/new
2. Import GitHub repository `ban-hang-online`
3. Configure Project:
   - **Framework Preset**: Other
   - **Root Directory**: `./`
   - **Build Command**: `npm run vercel-build`
   - **Output Directory**: `public`
   - **Install Command**: `npm install`

4. Environment Variables (Thêm tất cả):

```bash
APP_NAME=BanHangOnline
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=https://ban-hang-online.vercel.app

DB_CONNECTION=pgsql
DB_HOST=db.YOUR_PROJECT_REF.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=YOUR_SUPABASE_PASSWORD

CACHE_DRIVER=array
SESSION_DRIVER=cookie
QUEUE_CONNECTION=sync

SUPABASE_URL=https://YOUR_PROJECT_REF.supabase.co
SUPABASE_KEY=YOUR_SUPABASE_ANON_KEY
SUPABASE_SERVICE_KEY=YOUR_SUPABASE_SERVICE_KEY
```

5. Click **Deploy**

### 4.4 Tự Động Generate APP_KEY

```bash
# Tạo script để generate APP_KEY
cat > generate_app_key.sh << 'EOF'
#!/bin/bash

# Generate Laravel APP_KEY
php artisan key:generate --show

echo ""
echo "📋 Copy key trên và add vào Vercel Environment Variables:"
echo "   APP_KEY=base64:..."
EOF

chmod +x generate_app_key.sh
./generate_app_key.sh
```

### 4.5 Setup Custom Domain (Optional)

**Qua Vercel Dashboard:**
1. Project Settings → Domains
2. Add domain: `yourdomain.com`
3. Configure DNS:
   - Type: `A` Record
   - Name: `@`
   - Value: `76.76.21.21`
   - Type: `CNAME`
   - Name: `www`
   - Value: `cname.vercel-dns.com`

**Qua CLI:**

```bash
vercel domains add yourdomain.com
vercel domains inspect yourdomain.com
```

### 4.6 Setup Environment Cho Multiple Environments

```bash
# Development
vercel env add APP_ENV development

# Staging  
vercel env add APP_ENV preview

# Production
vercel env add APP_ENV production

# Pull environment variables về local
vercel env pull .env.vercel
```

---

## ⚙️ 5. Tự Động Hóa CI/CD

### 5.1 GitHub Actions Workflow

**Tạo file .github/workflows/deploy.yml:**

```bash
mkdir -p .github/workflows

cat > .github/workflows/deploy.yml << 'EOF'
name: Deploy to Vercel

on:
  push:
    branches:
      - main
      - develop
  pull_request:
    branches:
      - main

env:
  VERCEL_ORG_ID: ${{ secrets.VERCEL_ORG_ID }}
  VERCEL_PROJECT_ID: ${{ secrets.VERCEL_PROJECT_ID }}

jobs:
  test:
    name: Run Tests
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v4
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mbstring, xml, ctype, json, pgsql
          
      - name: Setup Node.js
        uses: actions/setup-node@v4
        with:
          node-version: '20'
          cache: 'npm'
          
      - name: Install PHP dependencies
        run: composer install --no-interaction --prefer-dist
        
      - name: Install Node dependencies
        run: npm ci
        
      - name: Create .env
        run: |
          cp .env.example .env
          php artisan key:generate
          
      - name: Run PHP tests
        run: vendor/bin/phpunit
        
      - name: Build assets
        run: npm run build
        
      - name: Install Playwright
        run: npx playwright install --with-deps
        
      - name: Run E2E tests
        run: npm run test:e2e

  deploy-preview:
    name: Deploy Preview
    runs-on: ubuntu-latest
    needs: test
    if: github.event_name == 'pull_request'
    
    steps:
      - uses: actions/checkout@v4
      
      - name: Install Vercel CLI
        run: npm install --global vercel@latest
        
      - name: Pull Vercel Environment
        run: vercel pull --yes --environment=preview --token=${{ secrets.VERCEL_TOKEN }}
        
      - name: Build Project
        run: vercel build --token=${{ secrets.VERCEL_TOKEN }}
        
      - name: Deploy to Vercel
        id: deploy
        run: |
          url=$(vercel deploy --prebuilt --token=${{ secrets.VERCEL_TOKEN }})
          echo "url=$url" >> $GITHUB_OUTPUT
          
      - name: Comment PR
        uses: actions/github-script@v7
        with:
          script: |
            github.rest.issues.createComment({
              issue_number: context.issue.number,
              owner: context.repo.owner,
              repo: context.repo.repo,
              body: '🚀 Preview deployed to: ${{ steps.deploy.outputs.url }}'
            })

  deploy-production:
    name: Deploy Production
    runs-on: ubuntu-latest
    needs: test
    if: github.ref == 'refs/heads/main' && github.event_name == 'push'
    
    steps:
      - uses: actions/checkout@v4
      
      - name: Install Vercel CLI
        run: npm install --global vercel@latest
        
      - name: Pull Vercel Environment
        run: vercel pull --yes --environment=production --token=${{ secrets.VERCEL_TOKEN }}
        
      - name: Build Project
        run: vercel build --prod --token=${{ secrets.VERCEL_TOKEN }}
        
      - name: Deploy to Vercel
        run: vercel deploy --prebuilt --prod --token=${{ secrets.VERCEL_TOKEN }}
        
  migrate-database:
    name: Run Database Migrations
    runs-on: ubuntu-latest
    needs: deploy-production
    if: github.ref == 'refs/heads/main' && github.event_name == 'push'
    
    steps:
      - uses: actions/checkout@v4
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          
      - name: Install dependencies
        run: composer install --no-dev --optimize-autoloader
        
      - name: Run migrations
        env:
          DB_CONNECTION: pgsql
          DB_HOST: ${{ secrets.DB_HOST }}
          DB_PORT: 5432
          DB_DATABASE: ${{ secrets.DB_DATABASE }}
          DB_USERNAME: ${{ secrets.DB_USERNAME }}
          DB_PASSWORD: ${{ secrets.DB_PASSWORD }}
        run: php artisan migrate --force
EOF
```

### 5.2 Thêm GitHub Secrets

```bash
# Lấy Vercel credentials
vercel whoami
vercel project ls

# Add vào GitHub:
# Settings → Secrets and variables → Actions → New repository secret
```

**Required Secrets:**
- `VERCEL_TOKEN`: (lấy từ https://vercel.com/account/tokens)
- `VERCEL_ORG_ID`: (lấy từ `.vercel/project.json`)
- `VERCEL_PROJECT_ID`: (lấy từ `.vercel/project.json`)
- `DB_HOST`: Supabase host
- `DB_DATABASE`: postgres
- `DB_USERNAME`: postgres
- `DB_PASSWORD`: Supabase password

**Script tự động add secrets:**

```bash
cat > add_github_secrets.sh << 'EOF'
#!/bin/bash

# Cần cài GitHub CLI: gh
gh secret set VERCEL_TOKEN
gh secret set VERCEL_ORG_ID
gh secret set VERCEL_PROJECT_ID
gh secret set DB_HOST
gh secret set DB_DATABASE
gh secret set DB_USERNAME
gh secret set DB_PASSWORD

echo "✅ All secrets added!"
EOF

chmod +x add_github_secrets.sh
```

### 5.3 Setup Dependabot

**Tạo file .github/dependabot.yml:**

```bash
cat > .github/dependabot.yml << 'EOF'
version: 2
updates:
  # Composer dependencies
  - package-ecosystem: "composer"
    directory: "/"
    schedule:
      interval: "weekly"
    open-pull-requests-limit: 10
    
  # npm dependencies
  - package-ecosystem: "npm"
    directory: "/"
    schedule:
      interval: "weekly"
    open-pull-requests-limit: 10
    
  # GitHub Actions
  - package-ecosystem: "github-actions"
    directory: "/"
    schedule:
      interval: "weekly"
EOF
```

### 5.4 Commit Tất Cả Config Files

```bash
git add .
git commit -m "feat: Add deployment configurations

- Added Vercel configuration
- Setup GitHub Actions CI/CD
- Added automated database migrations
- Configured Dependabot
- Ready for production deployment

Co-authored-by: Copilot <223556219+Copilot@users.noreply.github.com>"

git push origin main
```

---

## 🔍 6. Troubleshooting

### 6.1 Common Issues

#### Issue: Build Failed on Vercel

```bash
# Kiểm tra logs
vercel logs

# Test build locally
npm run vercel-build

# Kiểm tra PHP version
php -v

# Clear cache
php artisan cache:clear
php artisan config:clear
```

#### Issue: Database Connection Failed

```bash
# Test connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check environment variables
php artisan config:show database

# Test Supabase connection
psql "postgresql://postgres:[PASSWORD]@db.[PROJECT_REF].supabase.co:5432/postgres"
```

#### Issue: Assets Not Loading

```bash
# Rebuild assets
npm run build

# Check public path
php artisan config:cache

# Verify Vite manifest
cat public/build/manifest.json

# Check Vercel routes
cat vercel.json
```

### 6.2 Debug Commands

```bash
# Check Vercel deployment status
vercel inspect [DEPLOYMENT_URL]

# Check environment variables
vercel env ls

# Pull latest environment
vercel env pull

# Check Supabase project status
supabase projects list

# Check database migrations
supabase db diff

# View Supabase logs
supabase functions logs
```

### 6.3 Performance Optimization

**Enable caching:**

```bash
# On production
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear all caches
php artisan optimize:clear
```

**Vercel Edge Caching:**

```json
// Update vercel.json
{
  "headers": [
    {
      "source": "/build/(.*)",
      "headers": [
        {
          "key": "Cache-Control",
          "value": "public, max-age=31536000, immutable"
        }
      ]
    }
  ]
}
```

### 6.4 Monitoring & Logging

**Setup Vercel Analytics:**

```bash
npm install @vercel/analytics

# Add to resources/views/layouts/app.blade.php
<script>
  import { inject } from '@vercel/analytics';
  inject();
</script>
```

**Setup Error Tracking (Sentry):**

```bash
composer require sentry/sentry-laravel

php artisan sentry:publish --dsn=YOUR_SENTRY_DSN

# Add to .env
SENTRY_LARAVEL_DSN=your-sentry-dsn
SENTRY_TRACES_SAMPLE_RATE=1.0
```

---

## ✅ Checklist Hoàn Thành

### GitHub Setup
- [ ] Repository được tạo và code được push
- [ ] Branches (main, develop, staging) được setup
- [ ] Branch protection rules được cấu hình
- [ ] .gitignore được cấu hình đúng
- [ ] GitHub Secrets được thêm vào

### Supabase Setup
- [ ] Project được tạo thành công
- [ ] Database schema được migrate
- [ ] RLS policies được apply
- [ ] API keys được lưu an toàn
- [ ] Backup automation được setup

### Vercel Setup
- [ ] Project được deploy thành công
- [ ] Environment variables được cấu hình
- [ ] Custom domain được setup (nếu có)
- [ ] Build logs không có errors
- [ ] Website accessible và hoạt động bình thường

### CI/CD Setup
- [ ] GitHub Actions workflow được cấu hình
- [ ] Automated tests chạy thành công
- [ ] Auto deployment hoạt động
- [ ] Database migrations tự động chạy
- [ ] Dependabot được enable

---

## 📚 Tài Liệu Tham Khảo

- **Laravel Deployment**: https://laravel.com/docs/deployment
- **Vercel Documentation**: https://vercel.com/docs
- **Supabase Guides**: https://supabase.com/docs
- **GitHub Actions**: https://docs.github.com/actions
- **PostgreSQL on Supabase**: https://supabase.com/docs/guides/database

---

## 🎯 Quick Start Script

**Chạy tất cả trong một lệnh:**

```bash
cat > deploy_all.sh << 'DEPLOY_SCRIPT'
#!/bin/bash
set -e

echo "🚀 Starting full deployment..."

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m'

# 1. Git Setup
echo -e "${BLUE}📦 Step 1: Git Setup${NC}"
git add .
git commit -m "chore: Prepare for deployment" || true
git push origin main

# 2. Supabase Migrations
echo -e "${BLUE}🗄️  Step 2: Database Migrations${NC}"
supabase db push

# 3. Vercel Deployment
echo -e "${BLUE}🌐 Step 3: Deploy to Vercel${NC}"
vercel --prod

echo -e "${GREEN}✅ Deployment completed successfully!${NC}"
echo "Check your site at: https://ban-hang-online.vercel.app"
DEPLOY_SCRIPT

chmod +x deploy_all.sh
```

**Chạy:**
```bash
./deploy_all.sh
```

---

## 🆘 Hỗ Trợ

Nếu gặp vấn đề, check:
1. ✅ Vercel logs: `vercel logs`
2. ✅ GitHub Actions: Repository → Actions tab
3. ✅ Supabase Dashboard: https://app.supabase.com
4. ✅ Laravel logs: `storage/logs/laravel.log`

---

**🎉 Chúc mừng! Bạn đã hoàn thành deployment!**

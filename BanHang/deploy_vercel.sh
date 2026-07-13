#!/bin/bash
set -e

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${BLUE}🚀 Vercel Deployment Script${NC}\n"

# Step 1: Check if on correct branch
echo -e "${BLUE}📝 Step 1: Git Status${NC}"
CURRENT_BRANCH=$(git branch --show-current)
echo "Current branch: $CURRENT_BRANCH"

if [ "$CURRENT_BRANCH" != "main" ]; then
    echo -e "${YELLOW}⚠️  Not on main branch. Switch to main? (y/n)${NC}"
    read -r SWITCH
    if [ "$SWITCH" = "y" ]; then
        git checkout main
        git merge $CURRENT_BRANCH
    fi
fi

# Step 2: Add and commit changes
echo -e "\n${BLUE}📦 Step 2: Committing Changes${NC}"
git add vercel.json api/index.php build.sh .vercelignore VERCEL-DEPLOYMENT-GUIDE.md

git status --short

echo -e "${YELLOW}Commit message (or press Enter for default):${NC}"
read -r COMMIT_MSG

if [ -z "$COMMIT_MSG" ]; then
    COMMIT_MSG="deploy: Configure Vercel deployment"
fi

git commit -m "$COMMIT_MSG

- Added Vercel configuration files
- Created serverless function entry point
- Added build optimization script
- Updated deployment documentation

Co-authored-by: Copilot <223556219+Copilot@users.noreply.github.com>" || echo "No changes to commit"

# Step 3: Push to GitHub
echo -e "\n${BLUE}⬆️  Step 3: Pushing to GitHub${NC}"
git push origin $(git branch --show-current)

echo -e "${GREEN}✅ Code pushed to GitHub!${NC}"

# Step 4: Check Vercel CLI
echo -e "\n${BLUE}🔧 Step 4: Checking Vercel CLI${NC}"
if ! command -v vercel &> /dev/null; then
    echo -e "${YELLOW}Vercel CLI not found. Installing...${NC}"
    npm install -g vercel
fi

# Step 5: Deploy options
echo -e "\n${BLUE}🌐 Step 5: Deployment Options${NC}"
echo ""
echo "Chọn phương thức deploy:"
echo "1) Deploy qua Vercel Dashboard (khuyến nghị - dễ setup env vars)"
echo "2) Deploy qua Vercel CLI (nhanh nhưng cần setup env vars thủ công)"
echo "3) Chỉ push code, deploy sau"
echo ""
read -p "Chọn (1/2/3): " DEPLOY_METHOD

case $DEPLOY_METHOD in
    1)
        echo -e "\n${GREEN}📱 Deploy qua Dashboard:${NC}"
        echo ""
        echo "1. Truy cập: https://vercel.com/new"
        echo "2. Import repository: nvs15122k4/BanHang"
        echo "3. Configure:"
        echo "   - Framework: Other"
        echo "   - Build Command: ./build.sh"
        echo "   - Output Directory: public"
        echo "4. Add Environment Variables:"
        echo ""
        cat << 'ENV'
APP_NAME=BanHang
APP_ENV=production
APP_KEY=base64:rC2RMoIEmkZ3Gz6lMAxA6f5iJFGK23l7rYeom+8nF88=
APP_DEBUG=false
APP_URL=https://your-project.vercel.app
DB_CONNECTION=pgsql
DB_HOST=db.zegdcaqmhvydyxgxrjvt.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=Kirishima084009!?
CACHE_DRIVER=array
SESSION_DRIVER=cookie
NEXT_PUBLIC_SUPABASE_URL=https://zegdcaqmhvydyxgxrjvt.supabase.co
NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY=sb_publishable_f_X3W93mF48Y183YDKx3_g_WFD5LjUv
ENV
        echo ""
        echo "5. Click Deploy!"
        echo ""
        echo -e "${YELLOW}👉 Opening Vercel dashboard...${NC}"
        sleep 2
        xdg-open "https://vercel.com/new" 2>/dev/null || open "https://vercel.com/new" 2>/dev/null || echo "Please open: https://vercel.com/new"
        ;;
    
    2)
        echo -e "\n${BLUE}🚀 Deploying via CLI...${NC}"
        
        # Login to Vercel
        echo "Logging in to Vercel..."
        vercel login
        
        # Deploy
        echo -e "\n${YELLOW}⚠️  Note: You need to setup environment variables manually after deployment${NC}"
        echo "Deploying to production..."
        vercel --prod
        
        echo -e "\n${GREEN}✅ Deployed!${NC}"
        echo ""
        echo "Next steps:"
        echo "1. Add environment variables:"
        echo "   vercel env add APP_KEY"
        echo "   vercel env add DB_HOST"
        echo "   etc..."
        echo ""
        echo "2. Or add via dashboard:"
        echo "   https://vercel.com/dashboard"
        ;;
    
    3)
        echo -e "\n${GREEN}✅ Code pushed! Deploy later via:${NC}"
        echo "- Dashboard: https://vercel.com/new"
        echo "- CLI: vercel --prod"
        ;;
    
    *)
        echo -e "${RED}Invalid option${NC}"
        exit 1
        ;;
esac

echo -e "\n${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}🎉 Deployment process complete!${NC}"
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
echo "📚 Full guide: VERCEL-DEPLOYMENT-GUIDE.md"
echo "🌐 Repository: https://github.com/nvs15122k4/BanHang"
echo ""

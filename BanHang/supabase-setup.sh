#!/bin/bash
set -e

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${BLUE}🚀 Supabase Database Setup Script${NC}\n"

# Supabase Project Info
SUPABASE_URL="https://zegdcaqmhvydyxgxrjvt.supabase.co"
SUPABASE_PROJECT_REF="zegdcaqmhvydyxgxrjvt"
SUPABASE_ANON_KEY="sb_publishable_f_X3W93mF48Y183YDKx3_g_WFD5LjUv"

echo -e "${YELLOW}⚠️  QUAN TRỌNG: Bạn cần lấy Database Password từ Supabase${NC}"
echo ""
echo "Cách lấy Database Password:"
echo "1. Truy cập: https://supabase.com/dashboard/project/$SUPABASE_PROJECT_REF/settings/database"
echo "2. Tìm phần 'Connection string'"
echo "3. Click 'Reset database password' nếu chưa có"
echo "4. Copy password"
echo ""
read -sp "Nhập Database Password từ Supabase: " DB_PASSWORD
echo ""

if [ -z "$DB_PASSWORD" ]; then
    echo -e "${RED}❌ Database password không được để trống!${NC}"
    exit 1
fi

# Connection info
DB_HOST="db.$SUPABASE_PROJECT_REF.supabase.co"
DB_PORT="5432"
DB_DATABASE="postgres"
DB_USERNAME="postgres"

echo -e "\n${BLUE}📝 Cập nhật .env file...${NC}"

# Backup .env
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)

# Update .env for PostgreSQL
cat > .env.supabase << EOF
# Supabase PostgreSQL Connection
DB_CONNECTION=pgsql
DB_HOST=$DB_HOST
DB_PORT=$DB_PORT
DB_DATABASE=$DB_DATABASE
DB_USERNAME=$DB_USERNAME
DB_PASSWORD=$DB_PASSWORD

# Supabase Keys
NEXT_PUBLIC_SUPABASE_URL=$SUPABASE_URL
NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY=$SUPABASE_ANON_KEY
EOF

echo -e "${GREEN}✅ Created .env.supabase${NC}"

# Test connection
echo -e "\n${BLUE}🔌 Testing connection to Supabase...${NC}"

# Using psql
if command -v psql &> /dev/null; then
    export PGPASSWORD=$DB_PASSWORD
    if psql -h $DB_HOST -p $DB_PORT -U $DB_USERNAME -d $DB_DATABASE -c "SELECT version();" > /dev/null 2>&1; then
        echo -e "${GREEN}✅ Connection successful!${NC}"
    else
        echo -e "${RED}❌ Connection failed!${NC}"
        echo "Please check your password and network connection."
        exit 1
    fi
else
    echo -e "${YELLOW}⚠️  psql not found, skipping connection test${NC}"
    echo "Installing postgresql-client..."
    sudo apt-get update && sudo apt-get install -y postgresql-client
fi

echo -e "\n${BLUE}🗄️  Exporting current MySQL database...${NC}"

# Export MySQL schema and data
mysqldump -u banhang -pbanhang --complete-insert --skip-triggers --no-create-db banhang > mysql_export.sql

echo -e "${GREEN}✅ MySQL export complete: mysql_export.sql${NC}"

echo -e "\n${BLUE}🔄 You can now run migrations on Supabase${NC}"
echo ""
echo "Next steps:"
echo "1. Merge .env.supabase into your .env file"
echo "2. Run: php artisan migrate:fresh --force"
echo "3. Optional: Import data from MySQL"
echo ""
echo -e "${YELLOW}Would you like to proceed with migration? (y/n)${NC}"
read -r PROCEED

if [ "$PROCEED" = "y" ] || [ "$PROCEED" = "Y" ]; then
    # Merge env
    cat .env.supabase >> .env
    
    echo -e "\n${BLUE}🚀 Running migrations on Supabase...${NC}"
    php artisan config:clear
    php artisan migrate:fresh --force
    
    echo -e "${GREEN}✅ Migrations complete!${NC}"
    
    echo -e "\n${BLUE}📊 Database tables:${NC}"
    php artisan db:table --database=pgsql
fi

echo -e "\n${GREEN}🎉 Setup complete!${NC}"

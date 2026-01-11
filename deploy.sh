#!/bin/bash

# Laravel Deployment Script
# Run this script after initial server setup

set -e

echo "🚀 Starting Laravel Deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
APP_DIR="/var/www/paridharan-crm"
APP_USER="www-data"

# Check if running as root or with sudo
if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}Please run as root or with sudo${NC}"
    exit 1
fi

echo -e "${GREEN}Step 1: Installing dependencies...${NC}"
cd $APP_DIR
composer install --optimize-autoloader --no-dev --no-interaction

echo -e "${GREEN}Step 2: Installing NPM dependencies...${NC}"
npm install --production

echo -e "${GREEN}Step 3: Building assets...${NC}"
npm run build

echo -e "${GREEN}Step 4: Setting up environment...${NC}"
if [ ! -f .env ]; then
    cp .env.example .env
    echo -e "${YELLOW}⚠️  Please edit .env file with your production settings${NC}"
    echo "Press Enter to continue after editing .env..."
    read
fi

echo -e "${GREEN}Step 5: Generating application key...${NC}"
php artisan key:generate --force

echo -e "${GREEN}Step 6: Running migrations...${NC}"
php artisan migrate --force

echo -e "${GREEN}Step 7: Creating storage link...${NC}"
php artisan storage:link

echo -e "${GREEN}Step 8: Optimizing application...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo -e "${GREEN}Step 9: Setting permissions...${NC}"
chown -R $APP_USER:$APP_USER $APP_DIR
chmod -R 755 $APP_DIR
chmod -R 775 $APP_DIR/storage
chmod -R 775 $APP_DIR/bootstrap/cache

echo -e "${GREEN}Step 10: Restarting services...${NC}"
systemctl restart php8.2-fpm
systemctl restart nginx

echo -e "${GREEN}✅ Deployment completed successfully!${NC}"
echo -e "${YELLOW}Don't forget to:${NC}"
echo "1. Set up queue workers with Supervisor"
echo "2. Configure cron jobs"
echo "3. Install SSL certificate"
echo "4. Configure firewall"


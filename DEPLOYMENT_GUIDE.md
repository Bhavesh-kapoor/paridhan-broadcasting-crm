# Laravel Application Deployment Guide for VPS Server

This guide will help you deploy your Laravel CRM application to a VPS server with queue workers.

## Prerequisites
- VPS server with Ubuntu 20.04/22.04 or Debian 11/12
- Root or sudo access
- Domain name pointed to your VPS IP (optional but recommended)

---

## Step 1: Initial Server Setup

### 1.1 Update System
```bash
sudo apt update && sudo apt upgrade -y
```

### 1.2 Create Non-Root User (if needed)
```bash
sudo adduser deploy
sudo usermod -aG sudo deploy
su - deploy
```

---

## Step 2: Install Required Software

### 2.1 Install PHP and Extensions
```bash
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.1 or 8.2 (check your Laravel version requirements)
sudo apt install -y php8.2 php8.2-fpm php8.2-cli php8.2-common php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath php8.2-intl php8.2-readline

# Verify installation
php -v
```

### 2.2 Install Composer
```bash
cd ~
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
composer --version
```

### 2.3 Install MySQL/MariaDB
```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p
```

In MySQL:
```sql
CREATE DATABASE paridharan_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'paridharan_user'@'localhost' IDENTIFIED BY 'your_strong_password_here';
GRANT ALL PRIVILEGES ON paridharan_crm.* TO 'paridharan_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 2.4 Install Nginx
```bash
sudo apt install -y nginx
sudo systemctl start nginx
sudo systemctl enable nginx
```

### 2.5 Install Redis (for queues and caching)
```bash
sudo apt install -y redis-server
sudo systemctl start redis-server
sudo systemctl enable redis-server
```

### 2.6 Install Supervisor (for queue workers)
```bash
sudo apt install -y supervisor
sudo systemctl start supervisor
sudo systemctl enable supervisor
```

### 2.7 Install Node.js and NPM (if needed for frontend assets)
```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
node -v
npm -v
```

---

## Step 3: Deploy Your Application

### 3.1 Clone Your Repository
```bash
cd /var/www
sudo git clone https://github.com/yourusername/paridharan-crm.git
# OR upload your files via SFTP/SCP

sudo chown -R $USER:www-data /var/www/paridharan-crm
cd /var/www/paridharan-crm
```

### 3.2 Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build  # or npm run production
```

### 3.3 Set Up Environment File
```bash
cp .env.example .env
nano .env
```

Update your `.env` file with production settings:
```env
APP_NAME="Paridharan CRM"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=daily
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=paridharan_crm
DB_USERNAME=paridharan_user
DB_PASSWORD=your_strong_password_here

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail Configuration (Update with your SMTP settings)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mail_username
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# WhatsApp API Configuration (Update with your credentials)
WHATSAPP_API_KEY=your_api_key
WHATSAPP_BASE_URL=your_whatsapp_api_url
WABA_ID=your_waba_id
```

### 3.4 Generate Application Key
```bash
php artisan key:generate
```

### 3.5 Run Migrations
```bash
php artisan migrate --force
```

### 3.6 Create Storage Link
```bash
php artisan storage:link
```

### 3.7 Optimize Application
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3.8 Set Permissions
```bash
sudo chown -R www-data:www-data /var/www/paridharan-crm
sudo chmod -R 755 /var/www/paridharan-crm
sudo chmod -R 775 /var/www/paridharan-crm/storage
sudo chmod -R 775 /var/www/paridharan-crm/bootstrap/cache
```

---

## Step 4: Configure Nginx

### 4.1 Create Nginx Configuration
```bash
sudo nano /etc/nginx/sites-available/paridharan-crm
```

Add the following configuration:
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/paridharan-crm/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Increase upload size if needed
    client_max_body_size 100M;
}
```

### 4.2 Enable Site
```bash
sudo ln -s /etc/nginx/sites-available/paridharan-crm /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## Step 5: Set Up Queue Workers with Supervisor

### 5.1 Create Supervisor Configuration
```bash
sudo nano /etc/supervisor/conf.d/paridharan-crm-worker.conf
```

Add the following:
```ini
[program:paridharan-crm-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/paridharan-crm/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/paridharan-crm/storage/logs/worker.log
stopwaitsecs=3600
```

### 5.2 Start Supervisor
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start paridharan-crm-worker:*
sudo supervisorctl status
```

---

## Step 6: Set Up Cron Jobs

### 6.1 Edit Crontab
```bash
sudo crontab -e -u www-data
```

Add the following line:
```cron
* * * * * cd /var/www/paridharan-crm && php artisan schedule:run >> /dev/null 2>&1
```

Or if you have specific scheduled tasks, add them individually.

---

## Step 7: Install SSL Certificate (Let's Encrypt)

### 7.1 Install Certbot
```bash
sudo apt install -y certbot python3-certbot-nginx
```

### 7.2 Obtain SSL Certificate
```bash
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

### 7.3 Auto-Renewal (already set up by certbot)
```bash
sudo certbot renew --dry-run
```

---

## Step 8: Security Hardening

### 8.1 Configure Firewall
```bash
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw enable
sudo ufw status
```

### 8.2 Secure MySQL
```bash
sudo mysql_secure_installation
```

### 8.3 Disable Root Login (SSH)
```bash
sudo nano /etc/ssh/sshd_config
# Set: PermitRootLogin no
sudo systemctl restart sshd
```

---

## Step 9: Additional Queue Configuration

### 9.1 For Campaign Queue Jobs
If you have specific queue names, create additional supervisor configs:

```bash
sudo nano /etc/supervisor/conf.d/paridharan-crm-campaigns.conf
```

```ini
[program:paridharan-crm-campaigns]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/paridharan-crm/artisan queue:work redis --queue=campaigns --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/paridharan-crm/storage/logs/campaign-worker.log
stopwaitsecs=3600
```

Then:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start paridharan-crm-campaigns:*
```

---

## Step 10: Monitoring and Maintenance

### 10.1 Check Queue Status
```bash
php artisan queue:monitor redis
```

### 10.2 View Logs
```bash
tail -f /var/www/paridharan-crm/storage/logs/laravel.log
tail -f /var/www/paridharan-crm/storage/logs/worker.log
```

### 10.3 Check Supervisor Status
```bash
sudo supervisorctl status
```

### 10.4 Restart Services
```bash
# Restart Nginx
sudo systemctl restart nginx

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm

# Restart Queue Workers
sudo supervisorctl restart paridharan-crm-worker:*

# Restart Redis
sudo systemctl restart redis-server
```

---

## Step 11: Post-Deployment Checklist

- [ ] Application is accessible via domain
- [ ] SSL certificate is installed and working
- [ ] Database connection is working
- [ ] Queue workers are running
- [ ] Cron jobs are set up
- [ ] Logs are being written
- [ ] File uploads are working
- [ ] Email sending is configured
- [ ] WhatsApp API is configured
- [ ] Storage link is created
- [ ] Permissions are correct
- [ ] Firewall is configured

---

## Troubleshooting

### Queue Not Processing
```bash
# Check if workers are running
sudo supervisorctl status

# Restart workers
sudo supervisorctl restart all

# Check Redis connection
redis-cli ping
```

### Permission Issues
```bash
sudo chown -R www-data:www-data /var/www/paridharan-crm
sudo chmod -R 755 /var/www/paridharan-crm
sudo chmod -R 775 /var/www/paridharan-crm/storage
```

### Nginx 502 Bad Gateway
```bash
# Check PHP-FPM status
sudo systemctl status php8.2-fpm

# Check PHP-FPM socket
ls -la /var/run/php/php8.2-fpm.sock
```

### Clear All Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Useful Commands

```bash
# View real-time logs
tail -f storage/logs/laravel.log

# Check queue status
php artisan queue:work --once

# Run migrations
php artisan migrate --force

# Seed database (if needed)
php artisan db:seed --force

# Clear all caches
php artisan optimize:clear

# Re-optimize
php artisan optimize
```

---

## Backup Strategy

### Daily Database Backup
```bash
sudo crontab -e
```

Add:
```cron
0 2 * * * mysqldump -u paridharan_user -p'password' paridharan_crm > /backups/db_$(date +\%Y\%m\%d).sql
```

---

## Notes

- Replace `yourdomain.com` with your actual domain
- Replace `paridharan_user` and passwords with your actual credentials
- Adjust PHP version (8.2) based on your Laravel version requirements
- Monitor disk space regularly
- Set up automated backups
- Keep your system and packages updated

---

## Support

For issues specific to your application, check:
- Laravel logs: `storage/logs/laravel.log`
- Queue logs: `storage/logs/worker.log`
- Nginx logs: `/var/log/nginx/error.log`
- PHP-FPM logs: `/var/log/php8.2-fpm.log`


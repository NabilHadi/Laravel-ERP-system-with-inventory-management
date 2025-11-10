# Deploying Laravel ERP on Debian VPS - Complete Guide

## Prerequisites
- Debian 11 or 12 VPS with root access
- Domain name (or subdomain)
- SSH key for secure access
- Git repository access (already set up)

---

## Step 1: Initial Server Setup

### 1.1 Connect to Your Server
```bash
ssh root@your_vps_ip_address
```

### 1.2 Update System Packages
```bash
apt update
apt upgrade -y
apt autoremove -y
```

### 1.3 Set Timezone
```bash
timedatectl set-timezone Asia/Riyadh  # Change to your timezone
# List available timezones: timedatectl list-timezones
```

### 1.4 Setup Hostname
```bash
hostnamectl set-hostname muhaseb-pro
nano /etc/hosts
# Change localhost line to include your hostname:
# 127.0.0.1   localhost muhaseb-pro
```

---

## Step 2: Install Required Software

### 2.1 Install PHP 8.1 and Extensions

First, update package lists:
```bash
apt update
```

Add the Sury PHP repository with proper key handling:
```bash
apt install -y gnupg2
wget -qO /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/sury-php.list
apt update
```

**Alternative (if above doesn't work):** Install without signature verification:
```bash
echo "deb [trusted=yes] https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/sury-php.list
apt update
```

Now install PHP 8.1:
```bash
apt install -y php8.1-fpm php8.1-cli php8.1-mysql php8.1-mbstring \
    php8.1-xml php8.1-bcmath php8.1-tokenizer php8.1-curl \
    php8.1-zip php8.1-gd php8.1-intl
```

### 2.2 Verify PHP Installation
```bash
php -v
php -m  # List installed modules
```

### 2.3 Install Composer
```bash
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
composer --version
```

### 2.4 Install Node.js and npm
```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt install -y nodejs
node --version
npm --version
```

### 2.5 Install MariaDB Server (MySQL replacement on Debian)
```bash
apt install -y mariadb-server
```

**Secure MariaDB manually** (mysql_secure_installation not available on Debian Trixie):
```bash
# Login to MariaDB as root
sudo mysql -u root

# Run these SQL commands:
ALTER USER 'root'@'localhost' IDENTIFIED BY 'your_strong_password';
DELETE FROM mysql.user WHERE User='';
DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');
DROP DATABASE IF EXISTS test;
DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';
FLUSH PRIVILEGES;
EXIT;
```

**Or run as a single command:**
```bash
sudo mysql -u root -e "
ALTER USER 'root'@'localhost' IDENTIFIED BY 'your_strong_password';
DELETE FROM mysql.user WHERE User='';
DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');
DROP DATABASE IF EXISTS test;
DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';
FLUSH PRIVILEGES;
"
```

Verify MariaDB is running:
```bash
sudo systemctl status mariadb
```

### 2.6 Install Nginx
```bash
apt install -y nginx
systemctl start nginx
systemctl enable nginx
nginx -v
```

### 2.7 Install Git
```bash
apt install -y git
git --version
```

### 2.8 Install Certbot (for SSL)
```bash
apt install -y certbot python3-certbot-nginx
```

### 2.9 Install Supervisor (for Laravel queues - optional)
```bash
apt install -y supervisor
```

### 2.10 Install Curl and Other Utilities
```bash
apt install -y curl wget unzip vim nano htop
```

---

## Step 3: Create Application Directory and User

### 3.1 Create Application Directory
```bash
mkdir -p /var/www
cd /var/www
```

### 3.2 Create Non-Root User (Recommended for Security)
```bash
useradd -m -s /bin/bash www-user
usermod -aG sudo www-user
usermod -aG www-data www-user
```

### 3.3 Change Ownership of /var/www to www-user
```bash
chown www-user:www-user /var/www
```

### 3.4 Switch to New User
```bash
su - www-user
# Or: sudo su - www-user
```

---

## Step 4: Clone and Setup Laravel Project

### 4.1 Clone Repository (as www-user)
```bash
cd /var/www
git clone https://github.com/NabilHadi/Laravel-ERP-system-with-inventory-management.git muhaseb-pro
cd muhaseb-pro
```

**Note:** You're already logged in as `www-user` from Step 3.4, and `/var/www` is owned by `www-user` (Step 3.3), so no `sudo` is needed.

### 4.2 Install PHP Dependencies
```bash
composer install --optimize-autoloader --no-dev
```

### 4.3 Install Frontend Dependencies
```bash
npm install
npm run build
```

### 4.4 Setup Environment File
```bash
cp .env.example .env
```

### 4.5 Generate Application Key
```bash
php artisan key:generate
```

### 4.6 Edit .env File
```bash
nano .env
```

Configure the following variables:
```env
APP_NAME=MuhasebPro
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=muhaseb_pro
DB_USERNAME=muhaseb_user
DB_PASSWORD=your_strong_password_here

CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=cookie
SESSION_LIFETIME=120

# Mail configuration (optional, update later)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@muhaseb-pro.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

## Step 5: Setup MySQL Database

### 5.1 Create Database

Login to MariaDB with the password you set:
```bash
sudo mysql -u root -p
```

Enter your root password, then run:
```sql
CREATE DATABASE muhaseb_pro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'muhaseb_user'@'127.0.0.1' IDENTIFIED BY 'your_strong_database_password';
GRANT ALL PRIVILEGES ON muhaseb_pro.* TO 'muhaseb_user'@'127.0.0.1';
FLUSH PRIVILEGES;
EXIT;
```

### 5.2 Run Database Migrations
```bash
php artisan migrate --force
```

### 5.3 Seed Database (Create Admin User)
```bash
php artisan db:seed --class=AdminUserSeeder
```

Check what credentials were created:
```bash
php artisan tinker
>>> App\Models\User::all();
>>> exit
```

---

## Step 6: Configure File Permissions

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/muhaseb-pro

# Set correct permissions
sudo chmod -R 755 /var/www/muhaseb-pro
sudo chmod -R 777 /var/www/muhaseb-pro/storage
sudo chmod -R 777 /var/www/muhaseb-pro/bootstrap/cache

# Verify permissions
ls -la /var/www/muhaseb-pro/storage
ls -la /var/www/muhaseb-pro/bootstrap
```

---

## Step 7: Configure Nginx

### 7.1 Create Nginx Configuration File
```bash
sudo nano /etc/nginx/sites-available/muhaseb-pro
```

**IMPORTANT:** Before pasting, note that you'll need to replace `yourdomain.com` with your actual domain later.

Paste the following configuration:
```nginx
# Redirect HTTP to HTTPS
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;

    location / {
        return 301 https://$server_name$request_uri;
    }

    location /.well-known/acme-challenge/ {
        root /var/www/certbot;
    }
}

# HTTPS configuration
server {
    listen 443 ssl;
    listen [::]:443 ssl;
    http2 on;
    server_name yourdomain.com www.yourdomain.com;

    # SSL certificates (will be configured by Certbot)
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;

    # Security headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Permissions-Policy "geolocation=(), microphone=(), camera=()" always;

    # Root and index
    root /var/www/muhaseb-pro/public;
    index index.html index.htm index.php;
    charset utf-8;

    # Disable access to hidden files
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }

    # Remove trailing slashes
    rewrite ^/(.*)/$ /$1 permanent;

    # Main application routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Prevent access to .env and other sensitive files
    location ~ \.(env|json|lock)$ {
        deny all;
        access_log off;
        log_not_found off;
    }

    # Favicon and robots
    location = /favicon.ico {
        access_log off;
        log_not_found off;
    }

    location = /robots.txt {
        access_log off;
        log_not_found off;
    }

    # PHP configuration
    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        
        # Increase timeouts for long-running requests
        fastcgi_read_timeout 300s;
        fastcgi_connect_timeout 300s;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    # Logging
    access_log /var/log/nginx/muhaseb-pro_access.log;
    error_log /var/log/nginx/muhaseb-pro_error.log;

    # Increase upload file size
    client_max_body_size 50M;
}
```

### 7.2 Enable the Site
```bash
sudo ln -s /etc/nginx/sites-available/muhaseb-pro /etc/nginx/sites-enabled/
```

### 7.3 Remove Default Site (Optional)
```bash
sudo rm /etc/nginx/sites-enabled/default
```

### 7.4 Replace Domain in Nginx Configuration

Replace `yourdomain.com` with your actual domain:
```bash
sudo nano /etc/nginx/sites-available/muhaseb-pro
# Use Ctrl+W to search
# Find: yourdomain.com
# Replace with: your actual domain (e.g., muhasebpro.qatarcentral.cloudapp.azure.com)
# Save with Ctrl+X, then Y, then Enter
```

### 7.5 Verify Configuration Syntax (Before SSL)

Test the basic Nginx syntax without SSL certificate:
```bash
# Create a temporary self-signed cert for syntax testing (optional)
# Or just verify the syntax will work after certs are installed
sudo nginx -t
```

**If you see SSL certificate errors at this point, that's normal - continue to Step 8 to generate certificates.**

---

## Step 8: Generate SSL Certificate (BEFORE Nginx Restart)

### 8.1 Create SSL Certificate with Certbot

**IMPORTANT:** The `--standalone` method requires port 80 to be free. Since Nginx is running, you have two options:

**OPTION A: Temporarily Stop Nginx (Recommended)**

```bash
# Stop Nginx
sudo systemctl stop nginx

# Generate certificate using standalone method
sudo certbot certonly --standalone -d yourdomain.com -d www.yourdomain.com

# Example for Azure domain:
# sudo certbot certonly --standalone -d muhasebpro.qatarcentral.cloudapp.azure.com
```

Follow the prompts:
- Enter your email address
- Accept the Let's Encrypt terms
- Choose "Y" for sharing email with EFF

After certificate is generated, restart Nginx:
```bash
sudo systemctl restart nginx
```

**OPTION B: Comment Out SSL in Nginx Config First**

```bash
# Edit Nginx config
sudo nano /etc/nginx/sites-available/muhaseb-pro

# Find and comment out these lines (add # at the beginning):
# ssl_certificate /etc/letsencrypt/live/...
# ssl_certificate_key /etc/letsencrypt/live/...

# Save and exit (Ctrl+X, Y, Enter)

# Restart Nginx to apply changes
sudo systemctl restart nginx

# Now generate certificate with nginx method
sudo certbot certonly --nginx -d yourdomain.com -d www.yourdomain.com

# Uncomment the SSL lines you commented out
sudo nano /etc/nginx/sites-available/muhaseb-pro
# Uncomment the SSL certificate lines

# Restart Nginx with SSL enabled
sudo systemctl restart nginx
```

**Expected output:**
```
Successfully received certificate.
Certificate is saved at: /etc/letsencrypt/live/yourdomain.com/fullchain.pem
Key is saved at: /etc/letsencrypt/live/yourdomain.com/privkey.pem
```

### 7.6 Test Nginx Configuration (After SSL Certificate)

Now test Nginx with the SSL certificate installed:
```bash
sudo nginx -t
```

**Expected output:**
```
nginx: the configuration file /etc/nginx/nginx.conf syntax is ok
nginx: configuration will be successful
```

### 7.7 Restart Nginx
```bash
sudo systemctl restart nginx
sudo systemctl status nginx
```

---

## Step 9: Auto-Renewal Setup for SSL Certificates

### 9.1 Enable Certbot Auto-Renewal
```bash
sudo systemctl enable certbot.timer
sudo systemctl start certbot.timer
sudo systemctl status certbot.timer
```

### 9.2 Verify Certificate Installation
```bash
sudo certbot certificates
```

### 9.3 Setup Auto-Renewal Check
```bash
sudo systemctl status certbot.timer
sudo systemctl enable certbot.timer
```

**Note on Dry-Run Testing:**
If you get a "port 80 already in use" error when running `sudo certbot renew --dry-run`, that's normal and expected because Nginx is running. The actual renewal process is configured differently and will work fine through the systemd timer. The dry-run test can be skipped.

**Verify certificate is working:**
```bash
sudo certbot certificates
```

This should show your certificate with the renewal date.

---

## Step 10: Configure PHP-FPM

### 10.1 Edit PHP-FPM Configuration
```bash
sudo nano /etc/php/8.1/fpm/php.ini
```

Important settings to check/update:
```ini
upload_max_filesize = 50M
post_max_size = 50M
memory_limit = 256M
max_execution_time = 300
date.timezone = Asia/Riyadh
```

### 10.2 Edit PHP-FPM Pool Configuration
```bash
sudo nano /etc/php/8.1/fpm/pool.d/www.conf
```

Check these settings:
```ini
user = www-data
group = www-data
listen = /run/php/php8.1-fpm.sock
listen.owner = www-data
listen.group = www-data
```

### 10.3 Restart PHP-FPM
```bash
sudo systemctl restart php8.1-fpm
sudo systemctl status php8.1-fpm
```

---

## Step 11: Optimize Laravel for Production

### 11.1 Cache Configuration
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 11.2 Set Correct File Permissions
```bash
sudo chown -R www-data:www-data /var/www/muhaseb-pro
sudo find /var/www/muhaseb-pro -type f -exec chmod 644 {} \;
sudo find /var/www/muhaseb-pro -type d -exec chmod 755 {} \;
sudo chmod -R 777 /var/www/muhaseb-pro/storage
sudo chmod -R 777 /var/www/muhaseb-pro/bootstrap/cache
```

### 11.3 Verify Installation
```bash
php artisan about
```

---

## Step 12: Setup Automated Deployments (Optional)

### 12.1 Create Deployment Script
```bash
sudo nano /var/www/muhaseb-pro/deploy.sh
```

Paste:
```bash
#!/bin/bash

cd /var/www/muhaseb-pro

# Pull latest code
git pull origin main

# Install dependencies
composer install --optimize-autoloader --no-dev

# Install npm packages and build
npm install
npm run build

# Run migrations
php artisan migrate --force

# Clear caches
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
sudo chown -R www-data:www-data /var/www/muhaseb-pro
sudo chmod -R 777 /var/www/muhaseb-pro/storage
sudo chmod -R 777 /var/www/muhaseb-pro/bootstrap/cache

echo "Deployment complete!"
```

### 12.2 Make Script Executable
```bash
sudo chmod +x /var/www/muhaseb-pro/deploy.sh
```

### 12.3 Run Manual Deployment
```bash
cd /var/www/muhaseb-pro
./deploy.sh
```

---

## Step 13: Setup Logging and Monitoring

### 13.1 Check Application Logs
```bash
tail -f /var/www/muhaseb-pro/storage/logs/laravel.log
```

### 13.2 Check Nginx Logs
```bash
# Access logs
tail -f /var/log/nginx/muhaseb-pro_access.log

# Error logs
tail -f /var/log/nginx/muhaseb-pro_error.log
```

### 13.3 Monitor Server Resources
```bash
# Real-time monitoring
htop

# Disk usage
df -h

# Memory usage
free -h

# Check MySQL
sudo mysql -u root -p -e "SHOW PROCESSLIST;"
```

### 13.4 Setup Log Rotation (Optional)
```bash
sudo nano /etc/logrotate.d/muhaseb-pro
```

Paste:
```
/var/www/muhaseb-pro/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
    sharedscripts
    postrotate
        systemctl reload php8.1-fpm > /dev/null 2>&1 || true
    endscript
}
```

---

## Step 14: Setup Fail2Ban for DDoS/Bot Protection (Recommended)

Fail2ban monitors logs and automatically bans IPs attempting brute force attacks.

### 14.1 Install Fail2Ban
```bash
sudo apt install -y fail2ban
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### 14.2 Configure Fail2Ban for Nginx
Create a filter for login attempts:
```bash
sudo nano /etc/fail2ban/filter.d/nginx-http-auth.conf
```

Paste:
```
[Definition]
failregex = ^<HOST> - .* \[.*\] "POST /login HTTP/1\.[01]" 401
ignoreregex =
```

### 14.3 Create Fail2Ban Jail for Login Protection
```bash
sudo nano /etc/fail2ban/jail.d/muhaseb-pro.conf
```

Paste:
```
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[nginx-http-auth]
enabled = true
port = http,https
filter = nginx-http-auth
logpath = /var/log/nginx/muhaseb-pro_access.log
maxretry = 5
findtime = 600
bantime = 3600

[nginx-login-attempt]
enabled = true
port = http,https
filter = nginx-http-auth
logpath = /var/log/nginx/muhaseb-pro_error.log
maxretry = 5
findtime = 600
bantime = 3600
```

**Explanation:**
- `maxretry = 5`: Ban after 5 failed attempts
- `findtime = 600`: Within 10 minutes
- `bantime = 3600`: Ban for 1 hour

### 14.4 Restart Fail2Ban
```bash
sudo systemctl restart fail2ban
sudo systemctl status fail2ban
```

### 14.5 Monitor Fail2Ban
```bash
# Check banned IPs
sudo fail2ban-client status

# Check specific jail
sudo fail2ban-client status nginx-http-auth

# View Fail2Ban logs
sudo tail -f /var/log/fail2ban.log

# Unban an IP (if needed)
sudo fail2ban-client set nginx-http-auth unbanip <IP_ADDRESS>
```

---

## Step 15: Setup Cron Jobs for Laravel Scheduler (Optional)

### 14.1 Add Cron Job
```bash
sudo crontab -e
```

Add this line:
```
* * * * * php /var/www/muhaseb-pro/artisan schedule:run >> /dev/null 2>&1
```

This runs Laravel's scheduler every minute.

---

## Step 15: Setup MySQL Backups (Recommended)

### 15.1 Create Backup Script
```bash
sudo nano /usr/local/bin/backup-muhaseb.sh
```

Paste:
```bash
#!/bin/bash

BACKUP_DIR="/var/backups/muhaseb-pro"
DATABASE="muhaseb_pro"
DB_USER="muhaseb_user"
DB_PASS="your_password_here"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u $DB_USER -p$DB_PASS $DATABASE | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Keep only last 30 days
find $BACKUP_DIR -name "db_*.sql.gz" -mtime +30 -delete

echo "Backup completed: $BACKUP_DIR/db_$DATE.sql.gz"
```

### 15.2 Make Executable and Setup Cron
```bash
sudo chmod +x /usr/local/bin/backup-muhaseb.sh
sudo crontab -e
```

Add:
```
0 2 * * * /usr/local/bin/backup-muhaseb.sh
```

(Runs daily at 2 AM)

---

## Step 16: Verify Deployment

### 16.1 Test Application
Visit your domain in a browser: `https://yourdomain.com`

### 16.2 Login Test
- Use the admin credentials created by seeder
- Test product CRUD operations
- Check all navigation links work

### 16.3 Check Application Status
```bash
php artisan tinker
>>> App\Models\User::count();
>>> App\Models\Product::count();
>>> exit
```

### 16.4 Verify SSL Certificate
Visit https://yourdomain.com and check the SSL certificate is valid

---

## Step 17: Post-Deployment Checklist

- [ ] Domain configured and pointing to VPS IP
- [ ] SSL certificate installed and auto-renewing
- [ ] Database migrations completed successfully
- [ ] Admin user created and login working
- [ ] All navigation links functional
- [ ] Product CRUD operations working
- [ ] Nginx and PHP-FPM running
- [ ] File permissions set correctly
- [ ] Logs being created and rotating
- [ ] Backups scheduled
- [ ] Application caching enabled
- [ ] Monitoring tools in place

---

## Troubleshooting

### Application Shows Blank Page
```bash
# Check error logs
tail -f /var/www/muhaseb-pro/storage/logs/laravel.log
tail -f /var/log/nginx/muhaseb-pro_error.log

# Check permissions
ls -la /var/www/muhaseb-pro/storage
ls -la /var/www/muhaseb-pro/bootstrap/cache
```

### 502 Bad Gateway Error
```bash
# Restart PHP-FPM
sudo systemctl restart php8.1-fpm

# Check PHP-FPM socket
ls -la /run/php/php8.1-fpm.sock

# Check Nginx configuration
sudo nginx -t
```

### Database Connection Failed
```bash
# Test MariaDB connection
mysql -u muhaseb_user -p -h 127.0.0.1 muhaseb_pro

# Check .env credentials match
cat .env | grep DB_

# Verify MariaDB is running
sudo systemctl status mariadb

# Check MariaDB port
sudo netstat -tlnp | grep mariadb
```

### Permissions Denied on Storage
```bash
sudo chown -R www-data:www-data /var/www/muhaseb-pro/storage
sudo chmod -R 777 /var/www/muhaseb-pro/storage
```

### SSL Certificate Issues
```bash
# Renew certificate manually
sudo certbot renew

# Check certificate status
sudo certbot certificates

# View Nginx error for SSL
tail -f /var/log/nginx/muhaseb-pro_error.log
```

---

## Security Best Practices

1. **Keep System Updated**
   ```bash
   sudo apt update && apt upgrade -y
   ```

2. **Setup Firewall**
   ```bash
   sudo apt install -y ufw
   sudo ufw allow 22/tcp
   sudo ufw allow 80/tcp
   sudo ufw allow 443/tcp
   sudo ufw enable
   ```

3. **SSH Key Authentication Only**
   - Disable password authentication in `/etc/ssh/sshd_config`
   - Use only SSH keys for login

4. **Monitor Failed Login Attempts**
   ```bash
   sudo apt install -y fail2ban
   sudo systemctl enable fail2ban
   ```

5. **Setup Rate Limiting** (already in Nginx config)

6. **Regular Backups** (already configured)

7. **Monitor Application Logs** regularly

---

## Next Steps

1. Configure email for password resets and notifications
2. Setup API keys for external integrations
3. Configure payment gateway (if needed)
4. Setup uptime monitoring service
5. Configure CDN for static assets (optional)
6. Setup automated testing pipeline
7. Configure staging environment for testing before production

---

## Quick Reference Commands

```bash
# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.1-fpm
sudo systemctl restart mariadb

# Check service status
sudo systemctl status nginx
sudo systemctl status php8.1-fpm
sudo systemctl status mariadb

# View logs
tail -f /var/www/muhaseb-pro/storage/logs/laravel.log
tail -f /var/log/nginx/muhaseb-pro_error.log

# Deploy updates
cd /var/www/muhaseb-pro && ./deploy.sh

# Clear caches
php artisan cache:clear
php artisan config:cache

# Database
php artisan migrate
php artisan db:seed --class=AdminUserSeeder
```

---

**Author**: Laravel ERP System  
**Last Updated**: November 2025  
**Version**: 1.0

For support or issues, check the application logs and Nginx error logs for detailed information.

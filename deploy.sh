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
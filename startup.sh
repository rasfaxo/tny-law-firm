#!/bin/sh

echo "=== Starting Azure App Service Startup Script ==="
echo "App initializing at $(date)" >> /tmp/startup.log

# 1. Configure Nginx document root and URL rewriting for Laravel
if [ -f /home/site/wwwroot/default ]; then
    echo "Applying custom Nginx configuration..."
    cp /home/site/wwwroot/default /etc/nginx/sites-available/default
    service nginx reload || /usr/sbin/nginx -s reload || true
fi

# 2. Run Database Migrations natively within the PHP 8.4 runtime
echo "Running database migrations..."
php /home/site/wwwroot/artisan cache:clear
php /home/site/wwwroot/artisan migrate --force || true

# 3. Seed database accounts and demo testing dataset
echo "Seeding database for staging..."
php /home/site/wwwroot/artisan db:seed --class=DatabaseSeeder --force || true
# 4. Cache Laravel configuration, routes, and views for performance
echo "Caching configuration..."
php /home/site/wwwroot/artisan config:cache
echo "Caching routes..."
php /home/site/wwwroot/artisan route:cache
echo "Caching views..."
php /home/site/wwwroot/artisan view:cache

echo "=== Startup script completed successfully ==="

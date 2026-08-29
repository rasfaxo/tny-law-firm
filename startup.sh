#!/bin/sh

echo "=== Starting Azure App Service Startup Script ==="

# 1. Configure Nginx document root and URL rewriting for Laravel
if [ -f /home/site/wwwroot/default ]; then
    echo "Applying custom Nginx configuration..."
    cp /home/site/wwwroot/default /etc/nginx/sites-available/default
    service nginx reload || /usr/sbin/nginx -s reload || true
fi

# 2. Run Database Migrations natively within the PHP 8.4 runtime
echo "Running database migrations..."
php /home/site/wwwroot/artisan migrate --force

# 3. Seed database accounts and demo testing dataset
echo "Seeding database for staging..."
php /home/site/wwwroot/artisan db:seed --class=DatabaseSeeder --force || true

echo "=== Startup script completed successfully ==="

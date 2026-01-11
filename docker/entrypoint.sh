#!/bin/sh
set -e

echo "🚀 Starting Laravel application setup..."

# Ensure DB_DATABASE is set correctly
export DB_DATABASE=/var/www/html/storage/database.sqlite

# Create database file if it doesn't exist
if [ ! -f "$DB_DATABASE" ]; then
    echo "📁 Creating SQLite database file..."
    mkdir -p "$(dirname "$DB_DATABASE")"
    touch "$DB_DATABASE"
    chmod 664 "$DB_DATABASE"
fi

# Set proper permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Run migrations
echo "🗄️  Running migrations..."
php artisan migrate --force

# Check if Shield config exists, if not publish it
if [ ! -f "/var/www/html/config/filament-shield.php" ]; then
    echo "🛡️  Publishing Shield config..."
    php artisan vendor:publish --tag=filament-shield-config --force
fi

# Install Shield (generate permissions)
echo "🛡️  Installing Shield..."
php artisan shield:install --minimal

# Seed super admin user
echo "👤 Creating super admin user..."
php artisan db:seed --class=SuperAdminSeeder --force

# Clear all caches
echo "🧹 Clearing caches..."
php artisan optimize:clear

echo "✅ Setup complete! Starting services..."

# Start supervisord (nginx + php-fpm)
exec /usr/bin/supervisord -c /etc/supervisord.conf

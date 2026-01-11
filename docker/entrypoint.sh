#!/bin/sh
set -e

echo "🚀 Starting Laravel application setup..."

# Get database path from Laravel config
DB_PATH=$(php artisan tinker --execute="echo config('database.connections.sqlite.database');")

echo "📍 Database path: $DB_PATH"

# Create database file if it doesn't exist
if [ ! -f "$DB_PATH" ]; then
    echo "📁 Creating SQLite database file at $DB_PATH..."
    mkdir -p "$(dirname "$DB_PATH")"
    touch "$DB_PATH"
    chmod 664 "$DB_PATH"
    chown www-data:www-data "$DB_PATH"
else
    echo "✓ Database file already exists"
fi

# Set proper permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Publish Spatie Permission migrations
echo "📦 Publishing Spatie Permission migrations..."
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="permission-migrations" --force

# Run migrations
echo "🗄️  Running migrations..."
php artisan migrate --force

# Seed super admin user (this will also create the role)
echo "👤 Creating super admin user..."
php artisan db:seed --class=SuperAdminSeeder --force

# Clear all caches
echo "🧹 Clearing caches..."
php artisan optimize:clear

echo "✅ Setup complete! Starting services..."

# Start supervisord (nginx + php-fpm)
exec /usr/bin/supervisord -c /etc/supervisord.conf

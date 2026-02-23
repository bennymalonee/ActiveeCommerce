#!/bin/sh
set -e

cd /var/www/html

# Generate .env from environment if not present
if [ ! -f .env ]; then
    cat > .env << EOF
APP_NAME="${APP_NAME:-Active eCommerce CMS}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY:-}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_URL="${APP_URL:-http://localhost}"
APP_TIMEZONE="${APP_TIMEZONE:-UTC}"
SYSTEM_KEY="${SYSTEM_KEY:-}"
DEMO_MODE="${DEMO_MODE:-Off}"
LOG_CHANNEL="${LOG_CHANNEL:-stack}"
DB_CONNECTION="${DB_CONNECTION:-mysql}"
DB_HOST="${DB_HOST:-mysql}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-}"
DB_USERNAME="${DB_USERNAME:-}"
DB_PASSWORD="${DB_PASSWORD:-}"
BROADCAST_DRIVER="${BROADCAST_DRIVER:-log}"
CACHE_DRIVER="${CACHE_DRIVER:-file}"
QUEUE_CONNECTION="${QUEUE_CONNECTION:-sync}"
SESSION_DRIVER="${SESSION_DRIVER:-file}"
SESSION_LIFETIME="${SESSION_LIFETIME:-120}"
DEFAULT_LANGUAGE="${DEFAULT_LANGUAGE:-en}"
FILESYSTEM_DRIVER="${FILESYSTEM_DRIVER:-local}"
EOF
fi

# Generate APP_KEY if empty
if [ -z "$(grep '^APP_KEY=.\+' .env)" ]; then
    php artisan key:generate --force
fi

php artisan storage:link --force 2>/dev/null || true
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true

chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

exec "$@"

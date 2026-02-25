# AGENTS.md

## Cursor Cloud specific instructions

### Project overview
Active eCommerce CMS — a multi-vendor e-commerce platform built on **Laravel 10** (PHP 8.2) with Vue 2 / Bootstrap 4 frontend assets compiled via **Laravel Mix 6**.

### System dependencies (pre-installed in snapshot)
- PHP 8.2 with extensions: pdo_mysql, mbstring, xml, zip, gd, intl, bcmath, opcache, curl, fileinfo, exif, sqlite3
- Composer 2
- MariaDB 10.11
- Node.js 18 (via nvm, set as default)

### Important caveats

- **RouteServiceProvider state**: The repo ships with `RouteServiceProvider.php` in "install mode" (only install routes enabled). To run the app in post-install mode, copy `app/Providers/RouteServiceProvider.txt` over `app/Providers/RouteServiceProvider.php`. This is already done in the dev branch.
- **`public/index.php` is gitignored**: The `.gitignore` has `/public/*`. The app uses root `index.php` as the entry point (nginx serves from root). For `php artisan serve`, you must create `public/index.php` with paths adjusted to `__DIR__.'/../vendor/autoload.php'` and `__DIR__.'/../bootstrap/app.php'`.
- **MariaDB auth**: MariaDB root uses unix socket auth by default. Run `sudo mariadb -e "ALTER USER 'root'@'localhost' IDENTIFIED BY ''; FLUSH PRIVILEGES;"` to allow password-less TCP connections from the Laravel app.
- **No `composer.lock`**: The repo gitignores `*.lock`. Each `composer install` resolves fresh. The `meneses/laravel-mpdf` package was renamed to `carlos-meneses/laravel-mpdf` on Packagist (fixed in composer.json).
- **Database seeding**: Import `shop.sql` (MariaDB dump) into the `ecommerce_db` database. It includes demo categories, products, and seller/customer users but no admin user. Create one via artisan tinker with `user_type = 'admin'` and assign the `Super Admin` role.

### Running the application
```bash
# Start MariaDB
sudo service mariadb start

# Start Laravel dev server
cd /workspace && php artisan serve --host=0.0.0.0 --port=8000
```

### Running tests
```bash
cd /workspace && php artisan test
```

### Building frontend assets
```bash
# Development build
source $HOME/.nvm/nvm.sh && nvm use 18 && npx mix

# Watch mode
source $HOME/.nvm/nvm.sh && nvm use 18 && npx mix watch
```

### Admin credentials (dev only)
- Email: `admin@example.com`
- Password: `admin123`

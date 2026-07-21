# Installation Guide

## System Requirements

- **PHP**: 8.3 or higher
- **Database**: MySQL 8.0+ or SQLite 3
- **Node.js**: 18+ (optional, for frontend development)
- **Composer**: Latest version

## Step 1: Clone or Prepare the Project

```bash
cd /path/to/project
```

## Step 2: Install Dependencies

### PHP Dependencies
```bash
composer install
```

If you encounter issues, try:
```bash
composer install --no-interaction
composer dump-autoload
```

### JavaScript Dependencies (optional)
```bash
npm install
npm run build
```

## Step 3: Environment Configuration

### Copy the example environment file
```bash
cp .env.example .env
```

### Generate application key
```bash
php artisan key:generate
```

### Configure database in .env
For SQLite (quick development):
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

For MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=scada
DB_USERNAME=root
DB_PASSWORD=
```

## Step 4: Database Setup

### Create database file (SQLite)
```bash
touch database/database.sqlite
```

### Run migrations and seeders
```bash
php artisan migrate --seed
```

This will create all tables and seed the default operator account.

## Step 5: Start the Application

### Run the development server
```bash
php artisan serve
```

The app will be available at: **http://localhost:8000**

### Watch frontend assets (optional)
```bash
npm run dev
```

## Step 6: Login

- **URL**: http://localhost:8000
- **Email**: operator@scada.local
- **Password**: password

## Troubleshooting

### "Class not found" errors
```bash
composer dump-autoload
php artisan cache:clear
php artisan config:clear
```

### "No database connection" or migration errors
```bash
# Check database file exists (SQLite)
ls -la database/database.sqlite

# Check database connectivity
php artisan tinker
DB::connection()->getPdo()  # Should connect without error

# Try migrations with verbose output
php artisan migrate --seed -v
```

### Permission errors on storage/
```bash
# On Linux/macOS
chmod -R 775 storage bootstrap/cache

# Ensure web server user owns the directory
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Frontend assets not loading
```bash
npm install
npm run build
php artisan config:cache
```

### "The application has encountered an error. Please try again later."
```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## Production Deployment

### 1. Environment
```env
APP_ENV=production
APP_DEBUG=false
```

### 2. Optimize
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

### 3. Database
```bash
php artisan migrate --force
```

### 4. File Permissions
```bash
chmod -R 755 bootstrap/cache storage
chown -R www-data:www-data storage bootstrap/cache
```

### 5. Web Server Configuration

**Nginx** (`sites-available/scada.conf`):
```nginx
server {
    listen 80;
    server_name scada.local;
    
    root /var/www/scada/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }
}
```

**Apache** (`.htaccess` in `/public`):
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteRule ^index\.html$ - [L]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . /index.php [L]
</IfModule>
```

## Docker Deployment

```bash
docker-compose up -d

# Run migrations inside container
docker-compose exec app php artisan migrate --seed
```

## Running Tests

```bash
php artisan test
```

## API Testing

```bash
# Get current tank state
curl http://localhost:8000/api/tank

# Start filling
curl -X POST http://localhost:8000/api/start

# Get alarms
curl http://localhost:8000/api/alarms

# Stop process
curl -X POST http://localhost:8000/api/stop
```

## Additional Commands

```bash
# Clear all caches
php artisan optimize:clear

# Create a new model with migration
php artisan make:model ModelName -m

# Create a new controller
php artisan make:controller ControllerName

# Database tinker shell
php artisan tinker

# View all routes
php artisan route:list

# Check Laravel version
php artisan --version
```

## Support

For issues or questions:
1. Check the logs: `storage/logs/laravel.log`
2. Review [README.md](README.md)
3. Check [DATABASE_SCHEMA.md](DATABASE_SCHEMA.md)
4. Laravel docs: https://laravel.com/docs/12.x

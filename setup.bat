@echo off
REM Tank Level SCADA Simulator - Quick Setup Script for Windows

echo.
echo 🚀 Tank Level SCADA Simulator Setup
echo ======================================

REM Check PHP version
echo ✓ Checking PHP installation...
php -v >nul 2>&1 || (
    echo ❌ PHP 8.3+ is required
    exit /b 1
)

REM Check Composer
echo ✓ Checking Composer...
composer --version >nul 2>&1 || (
    echo ❌ Composer is required
    exit /b 1
)

REM Check Node.js
echo ✓ Checking Node.js...
node -v >nul 2>&1 || (
    echo ⚠️  Node.js not found ^(optional for frontend^)
)

REM Install PHP dependencies
echo.
echo 📦 Installing PHP dependencies...
call composer install || (
    echo ❌ Composer install failed
    exit /b 1
)

REM Generate app key
echo 🔑 Generating application key...
php artisan key:generate

REM Set up database
echo.
echo 🗄️  Setting up database...
if not exist "database\database.sqlite" (
    echo Creating SQLite database...
    type nul > database\database.sqlite
)

REM Run migrations
echo 🔄 Running migrations...
php artisan migrate --seed || (
    echo ⚠️  Migration warning ^(might need manual DB setup^)
)

REM Install Node dependencies
where npm >nul 2>&1
if %errorlevel% equ 0 (
    echo.
    echo 📦 Installing Node dependencies...
    call npm install
    
    echo.
    echo 🎨 Building frontend assets...
    call npm run build
)

echo.
echo ✅ Setup complete!
echo.
echo 🎯 Next steps:
echo    1. Start development server:  php artisan serve
echo    2. Watch frontend assets:      npm run dev ^(in another terminal^)
echo    3. Open browser:               http://localhost:8000
echo.
echo 📝 Default credentials:
echo    Email:    operator@scada.local
echo    Password: password
echo.
pause

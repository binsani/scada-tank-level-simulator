#!/bin/bash

# Tank Level SCADA Simulator - Quick Setup Script

echo "🚀 Tank Level SCADA Simulator Setup"
echo "======================================"

# Check PHP version
echo "✓ Checking PHP installation..."
php -v || { echo "❌ PHP 8.3+ is required"; exit 1; }

# Check Composer
echo "✓ Checking Composer..."
composer --version || { echo "❌ Composer is required"; exit 1; }

# Check Node.js
echo "✓ Checking Node.js..."
node -v || { echo "⚠️  Node.js not found (optional for frontend)"; }

# Install PHP dependencies
echo ""
echo "📦 Installing PHP dependencies..."
composer install || { echo "❌ Composer install failed"; exit 1; }

# Generate app key
echo "🔑 Generating application key..."
php artisan key:generate

# Set up database
echo ""
echo "🗄️  Setting up database..."
if [ ! -f "database/database.sqlite" ]; then
    echo "Creating SQLite database..."
    touch database/database.sqlite
fi

# Run migrations
echo "🔄 Running migrations..."
php artisan migrate --seed || { echo "⚠️  Migration warning (might need manual DB setup)"; }

# Install Node dependencies
if command -v npm &> /dev/null; then
    echo ""
    echo "📦 Installing Node dependencies..."
    npm install
    
    echo ""
    echo "🎨 Building frontend assets..."
    npm run build
fi

echo ""
echo "✅ Setup complete!"
echo ""
echo "🎯 Next steps:"
echo "  1. Start development server:  php artisan serve"
echo "  2. Watch frontend assets:      npm run dev (in another terminal)"
echo "  3. Open browser:               http://localhost:8000"
echo ""
echo "📝 Default credentials:"
echo "  Email:    operator@scada.local"
echo "  Password: password"

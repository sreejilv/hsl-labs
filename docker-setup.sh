#!/bin/bash

# HSL Labs Docker Setup Script
echo "🧬 HSL Labs - Docker Setup"
echo "=========================="

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker first."
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

# Copy environment file
echo "📋 Setting up environment file..."
if [ ! -f .env ]; then
    cp .env.docker .env
    echo "✅ Environment file created from .env.docker"
else
    echo "⚠️  .env file already exists. Skipping..."
fi

# Build and start containers
echo "🔨 Building and starting Docker containers..."
docker-compose up -d --build

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
sleep 30

# Install Composer dependencies
echo "📦 Installing Composer dependencies..."
docker-compose exec app composer install --optimize-autoloader

# Generate application key
echo "🔑 Generating application key..."
docker-compose exec app php artisan key:generate

# Run migrations and seeders
echo "🗄️  Running database migrations and seeders..."
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed --force

# Install NPM dependencies and build assets
echo "🎨 Installing NPM dependencies and building assets..."
docker-compose exec node npm install
docker-compose exec node npm run build

# Set proper permissions
echo "🔒 Setting proper permissions..."
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chown -R www-data:www-data /var/www/html/bootstrap/cache
docker-compose exec app chmod -R 775 /var/www/html/storage
docker-compose exec app chmod -R 775 /var/www/html/bootstrap/cache

# Clear caches
echo "🧹 Clearing application caches..."
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

echo ""
echo "🎉 HSL Labs is now running!"
echo ""
echo "📍 Application URLs:"
echo "   🌐 Main Application: http://localhost"
echo "   🗄️  PHPMyAdmin: http://localhost:8080"
echo "   📧 MailHog: http://localhost:8025"
echo ""
echo "🔐 Test Credentials:"
echo "   👨‍⚕️ Surgeon: surgeon@example.com / surgeon123"
echo "   👩‍💼 Staff: staff@example.com / staff123"
echo "   👨‍💻 Admin: admin@example.com / admin123"
echo ""
echo "🗄️  Database Connection:"
echo "   Host: localhost:3306"
echo "   Database: hsl_labs"
echo "   Username: hsl_user"
echo "   Password: hsl_password"
echo ""
echo "🐳 Docker Commands:"
echo "   Stop: docker-compose down"
echo "   Restart: docker-compose restart"
echo "   Logs: docker-compose logs -f"
echo "   Shell: docker-compose exec app bash"
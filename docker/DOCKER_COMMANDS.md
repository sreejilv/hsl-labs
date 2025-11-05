# HSL Labs - Docker Commands Reference

## Quick Start

```bash
# One-command setup
./docker-setup.sh

# Or manual setup
docker-compose up -d --build
docker-compose exec app php artisan migrate --seed
```

## Daily Operations

### Container Management

```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# Restart specific service
docker-compose restart app

# View logs
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f mysql

# Check container status
docker-compose ps
```

### Application Management

```bash
# Access application shell
docker-compose exec app bash

# Run Artisan commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:cache

# Install/Update dependencies
docker-compose exec app composer install
docker-compose exec app composer update

# Build assets
docker-compose exec node npm install
docker-compose exec node npm run build
docker-compose exec node npm run dev
```

### Database Management

```bash
# Access MySQL shell
docker-compose exec mysql mysql -u hsl_user -p hsl_labs

# Database backup
docker-compose exec mysql mysqldump -u hsl_user -p hsl_labs > backup.sql

# Database restore
docker-compose exec -T mysql mysql -u hsl_user -p hsl_labs < backup.sql

# Reset database
docker-compose exec app php artisan migrate:fresh --seed
```

### Queue Management

```bash
# Monitor queue
docker-compose logs -f queue

# Restart queue worker
docker-compose restart queue

# Process failed jobs
docker-compose exec app php artisan queue:retry all
```

### File Permissions

```bash
# Fix permissions
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chown -R www-data:www-data /var/www/html/bootstrap/cache
docker-compose exec app chmod -R 775 /var/www/html/storage
docker-compose exec app chmod -R 775 /var/www/html/bootstrap/cache
```

## Troubleshooting

### Common Issues

```bash
# Clear all caches
docker-compose exec app php artisan optimize:clear

# Rebuild containers
docker-compose down
docker-compose up -d --build --force-recreate

# Reset everything
docker-compose down -v
docker system prune -f
./docker-setup.sh
```

### Performance Optimization

```bash
# Optimize application
docker-compose exec app php artisan optimize

# Clear logs
docker-compose exec app php artisan log:clear

# Update Composer autoloader
docker-compose exec app composer dump-autoload -o
```

## Service URLs

-   **Application**: http://localhost
-   **PHPMyAdmin**: http://localhost:8080
-   **MailHog**: http://localhost:8025

## Database Connection

-   **Host**: localhost
-   **Port**: 3306
-   **Database**: hsl_labs
-   **Username**: hsl_user
-   **Password**: hsl_password

## Test Credentials

-   **Surgeon**: surgeon@example.com / surgeon123
-   **Staff**: staff@example.com / staff123
-   **Admin**: admin@example.com / admin123

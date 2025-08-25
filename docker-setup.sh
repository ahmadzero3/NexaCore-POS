#!/bin/bash

# Docker setup script for NexaCore POS
# This script ensures proper permissions and setup for the Laravel application

echo "Setting up NexaCore POS Docker environment..."

# Create necessary directories if they don't exist
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set proper permissions for Laravel directories
echo "Setting permissions for Laravel directories..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Copy environment file if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating .env file from .env.example..."
    cp .env.example .env
fi

# Copy .env.docker file if it doesn't exist
if [ ! -f .env.docker ]; then
    echo "Copying .env.docker.example to .env.docker..."
    cp .env.docker.example .env.docker
    echo "Docker port configuration created. Edit .env.docker to customize ports if needed."
fi

# Build and start Docker containers
echo "Building Docker containers..."
docker-compose down
docker-compose build --no-cache

echo "Starting Docker containers..."
docker-compose up -d

# Wait for containers to be ready
echo "Waiting for containers to start..."
sleep 10

# Install/update Composer dependencies
echo "Installing Composer dependencies..."
docker-compose exec app composer install --optimize-autoloader

# Generate application key
echo "Generating application key..."
docker-compose exec app php artisan key:generate

# Run database migrations
echo "Running database migrations..."
docker-compose exec app php artisan migrate --force

# Mark application as installed
echo "Marking application as installed..."
docker-compose exec app touch /var/www/html/storage/installed
docker-compose exec app chown www-data:www-data /var/www/html/storage/installed

# Optimize application
echo "Optimizing application..."
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Set installation status to true
echo "Updating installation status..."
docker-compose exec app php artisan config:clear

echo "Docker setup complete!"
echo "Application is available at: http://localhost"
# Read port from .env.docker file
EXTERNAL_DB_PORT=$(grep '^EXTERNAL_DB_PORT=' .env.docker 2>/dev/null | cut -d'=' -f2 || echo '5433')
WEB_PORT=$(grep '^WEB_PORT=' .env.docker 2>/dev/null | cut -d'=' -f2 || echo '80')

echo "Database is available at: localhost:${EXTERNAL_DB_PORT}"
echo "Web application is available at: http://localhost:${WEB_PORT}"
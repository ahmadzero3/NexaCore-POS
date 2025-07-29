# Makefile for NexaCore-POS Dockerized Application

.PHONY: all build up down install key migrate seed artisan clean dev prod

# Default target
all: dev

# Initialize the application (build, up, install, key, migrate)
init: build up install key migrate

# Build Docker images
build:
	docker-compose build

# Start Docker containers
up:
	docker-compose up -d

# Stop Docker containers
down:
	docker-compose down

# Install Composer dependencies inside the app container
install:
	docker-compose exec app composer install

# Generate Laravel application key
key:
	docker-compose exec app php artisan key:generate

# Run database migrations
migrate:
	docker-compose exec app php artisan migrate

# Run database seeders
seed:
	docker-compose exec app php artisan db:seed

# Run any php artisan command
artisan:
	docker-compose exec app php artisan $(filter-out $@,$(MAKECMDGOALS))

# Clean up Docker volumes and images
clean:
	docker-compose down -v --rmi all

# Development mode setup
dev: build up install key migrate
	@echo "\nNexaCore-POS development environment is ready! Access at http://localhost"

# Quick setup with seeding (bypasses web installer)
quick: build up install key migrate seed
	@echo "\nNexaCore-POS quick setup completed with sample data! Access at http://localhost"

# Production mode setup (requires a separate docker-compose.prod.yml or environment configuration)
# For a production setup, you would typically use a separate docker-compose.prod.yml
# with optimized images, persistent volumes, and proper environment variables.
# Example: docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d
prod: build up
	@echo "\nNexaCore-POS production environment is started. Ensure your .env is configured for production."
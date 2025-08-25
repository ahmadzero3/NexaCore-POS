# NexaCore POS - Default Data Initialization

This document explains how to initialize your NexaCore POS system with default data, similar to what happens during the installation process.

## Overview

The initialization process creates all the essential default data needed for the application to function properly, including:

- **Admin User**: Default administrator account (admin@example.com / 12345678)
- **Roles & Permissions**: Complete permission system setup
- **Application Settings**: Basic application configuration
- **Company Information**: Default company setup
- **Languages**: Default language configuration (English)
- **Templates**: SMS and Email templates
- **Financial Setup**: Account groups and payment types
- **Inventory Setup**: Default tax settings, item categories, units
- **Warehouse**: Default warehouse ("Main")
- **Location Data**: States/regions information
- **Version Information**: Application version tracking

## When to Use

Use the initialization scripts when:

- Setting up a fresh development environment
- Resetting the database to a clean state
- Creating a new instance with default data
- After running migrations on an empty database

## Methods

### Method 1: Shell Script (Recommended)

Use the provided shell script for a complete initialization with Docker environment checks:

```bash
# Make sure Docker containers are running
docker-compose up -d

# Run the initialization script
./init-default-data.sh
```

**Features:**
- ✅ Checks if Docker containers are running
- ✅ Verifies database connectivity
- ✅ Runs migrations automatically
- ✅ Seeds all default data
- ✅ Clears application cache
- ✅ Clears Laravel console sessions to ensure clean termination
- ✅ Configures installation status via environment variables
- ✅ Provides detailed progress feedback

### Method 2: Laravel Artisan Command

Use the Laravel artisan command for more control and integration:

```bash
# Basic initialization
docker-compose exec app php artisan init:default-data

# Force initialization (skip confirmation if data exists)
docker-compose exec app php artisan init:default-data --force

# Get help
docker-compose exec app php artisan init:default-data --help
```

**Features:**
- ✅ Interactive confirmation if data already exists
- ✅ Force option to skip confirmations
- ✅ Integrated with Laravel's command system
- ✅ Detailed error handling and reporting
- ✅ Progress indicators with emojis

### Method 3: Manual Database Seeding

For advanced users who want to run specific parts:

```bash
# Run migrations only
docker-compose exec app php artisan migrate --force

# Run main database seeder
docker-compose exec app php artisan db:seed --class=DatabaseSeeder --force

# Run version seeder (for version tracking)
docker-compose exec app php artisan db:seed --class=VersionSeeder --force

# Clear cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan view:clear
```

## Prerequisites

1. **Docker Environment**: Ensure Docker and Docker Compose are installed
2. **Running Containers**: Database and application containers must be running
3. **Database Access**: PostgreSQL database must be accessible
4. **Environment Configuration**: `.env` file must be properly configured

## Verification

After initialization, verify the setup:

1. **Check Application Access**:
   ```bash
   curl -I http://localhost
   ```
   Should return a 302 redirect to `/login`

2. **Verify Database Data**:
   ```bash
   # Check if admin user exists
   docker-compose exec db psql -U postgres -d laravel -c "SELECT email FROM users WHERE id = 1;"
   
   # Check application settings
   docker-compose exec db psql -U postgres -d laravel -c "SELECT application_name FROM app_settings;"
   
   # Check version information
   docker-compose exec db psql -U postgres -d laravel -c "SELECT version FROM versions ORDER BY id DESC LIMIT 1;"
   ```

3. **Login Test**:
   - Navigate to `http://localhost`
   - Login with: `admin@example.com` / `12345678`

## Troubleshooting

### Common Issues

**Database Connection Error**:
```bash
# Check if containers are running
docker-compose ps

# Restart containers if needed
docker-compose down && docker-compose up -d
```

**Migration Errors**:
```bash
# Check migration status
docker-compose exec app php artisan migrate:status

# Reset migrations (WARNING: This will drop all data)
docker-compose exec app php artisan migrate:fresh
```

**Permission Errors**:
```bash
# Fix file permissions
sudo chown -R $USER:$USER storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

**Cache Issues**:
```bash
# Clear all caches
docker-compose exec app php artisan optimize:clear
```

**Installation Status Column Error**:
- This has been fixed in the current version
- Installation status is managed via the `.env` file (`INSTALLATION_STATUS=true`)
- No database column update is required

### Data Already Exists

If you get a warning about existing data:

- Use `--force` flag to skip confirmation
- Or manually clear the database first:
  ```bash
  docker-compose exec app php artisan migrate:fresh
  ```

## Security Notes

⚠️ **Important**: The default admin credentials are:
- **Email**: admin@example.com
- **Password**: 12345678

**Always change these credentials in production environments!**

## Files Created/Modified

- `init-default-data.sh` - Shell script for initialization
- `app/Console/Commands/InitDefaultDataCommand.php` - Laravel artisan command
- Database tables populated with default data
- Application cache cleared
- Installation status updated

## Support

If you encounter issues:

1. Check the Docker container logs:
   ```bash
   docker-compose logs app
   docker-compose logs db
   ```

2. Verify your `.env` configuration
3. Ensure all prerequisites are met
4. Check the Laravel application logs in `storage/logs/`
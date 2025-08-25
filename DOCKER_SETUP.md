# Docker Setup for NexaCore POS

This document explains the Docker setup and permission fixes implemented for the NexaCore POS application.

## Permission Issues Fixed

The following permission issues have been resolved:

1. **User ID Mapping**: Created proper user mapping between host and container
2. **Storage Permissions**: Fixed Laravel storage and cache directory permissions
3. **Volume Mounting**: Optimized volume mounting with delegated option for better performance
4. **Entrypoint Script**: Added automatic permission fixing on container startup

## Quick Start

### Option 1: Automated Setup (Recommended)

Run the automated setup script:

```bash
./docker-setup.sh
```

This script will:
- Create necessary directories
- Set proper permissions
- Build and start Docker containers
- Install dependencies
- Run database migrations
- Optimize the application

### Option 2: Manual Setup

1. **Build and start containers:**
   ```bash
   docker-compose build
   docker-compose up -d
   ```

2. **Install dependencies:**
   ```bash
   docker-compose exec app composer install
   ```

3. **Generate application key:**
   ```bash
   docker-compose exec app php artisan key:generate
   ```

4. **Run migrations:**
   ```bash
   docker-compose exec app php artisan migrate
   ```

## Services

- **app**: PHP-FPM service running Laravel application
- **web**: Nginx web server
- **db**: PostgreSQL database

## Port Configuration

### Centralized Port Management
All Docker ports are now centrally configured in the `.env.docker` file. To change any port:

1. Edit `.env.docker` file
2. Modify the desired port values
3. Restart Docker containers: `docker-compose down && docker-compose up -d`

### Default Ports

#### Web Server (Nginx)
- Default Port: `80` (configurable via `WEB_PORT` in `.env.docker`)
- Access: http://localhost

#### PHP-FPM
- Default Port: `9000` (configurable via `PHP_FPM_PORT` in `.env.docker`)
- Note: Usually not accessed directly

#### PostgreSQL Database
- Database: `laravel`
- Username: `postgres`
- Password: `postgres`
- Host: `db` (internal) / `localhost` (external)
- External Port: `5433` (configurable via `EXTERNAL_DB_PORT` in `.env.docker`)
- Internal Port: `5432` (configurable via `INTERNAL_DB_PORT` in `.env.docker`)

## Environment Variables

The application uses the following database configuration:
- Database: `laravel`
- Username: `postgres`
- Password: `postgres`
- Host: `db`
- Port: `5433` (external), `5432` (internal)

## Permission Fixes Implemented

### 1. Dockerfile.app Changes

- Added proper user creation with UID/GID 1000
- Created entrypoint script for automatic permission fixing
- Improved directory structure and permissions

### 2. docker-compose.yml Changes

- Added delegated volume mounting for better performance
- Removed conflicting user directives
- Optimized service dependencies

### 3. Additional Files

- **`.dockerignore`**: Optimizes build performance by excluding unnecessary files
- **`docker-setup.sh`**: Automated setup script for easy deployment

## Troubleshooting

### Permission Denied Errors

If you encounter permission errors:

1. **Rebuild containers:**
   ```bash
   docker-compose down
   docker-compose build --no-cache
   docker-compose up -d
   ```

2. **Fix permissions manually:**
   ```bash
   docker-compose exec app chown -R www-data:www-data /var/www/html/storage
   docker-compose exec app chmod -R 775 /var/www/html/storage
   ```

### Database Connection Issues

If database connection fails:

1. **Check database status:**
   ```bash
   docker-compose ps
   ```

2. **View database logs:**
   ```bash
   docker-compose logs db
   ```

3. **Restart database:**
   ```bash
   docker-compose restart db
   ```

### Application Logs

View application logs:
```bash
docker-compose logs app
docker-compose logs web
```

## Development

For development, you can:

1. **Access the application container:**
   ```bash
   docker-compose exec app bash
   ```

2. **Run Artisan commands:**
   ```bash
   docker-compose exec app php artisan [command]
   ```

3. **Install new packages:**
   ```bash
   docker-compose exec app composer require [package]
   ```

## Production Considerations

For production deployment:

1. Update environment variables in `.env`
2. Use proper SSL certificates
3. Configure proper database credentials
4. Set `APP_ENV=production`
5. Enable opcache and other optimizations

## Security Notes

- The setup uses standard ports and credentials for development
- Change default passwords before production deployment
- Review and update security headers in nginx.conf
- Ensure proper firewall configuration in production
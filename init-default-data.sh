#!/bin/bash

# NexaCore POS - Initialize Default Data Script
# This script creates the same default data as during installation

echo "🚀 Initializing NexaCore POS with default data..."
echo "================================================"

# Check if Docker containers are running
if ! docker-compose ps | grep -q "Up"; then
    echo "❌ Docker containers are not running. Please start them first with:"
    echo "   docker-compose up -d"
    exit 1
fi

# Check if database exists and is accessible
echo "📊 Checking database connection..."
if ! docker-compose exec db psql -U postgres -d laravel -c "SELECT 1;" > /dev/null 2>&1; then
    echo "❌ Cannot connect to database. Please ensure the database is running and accessible."
    exit 1
fi

echo "✅ Database connection successful"

# Run migrations first
echo "🔄 Running database migrations..."
docker-compose exec app php artisan migrate --force

if [ $? -ne 0 ]; then
    echo "❌ Migration failed. Please check the error above."
    exit 1
fi

echo "✅ Migrations completed successfully"

# Run the main database seeder (creates all default data)
echo "🌱 Seeding database with default data..."
docker-compose exec app php artisan db:seed --class=DatabaseSeeder --force

if [ $? -ne 0 ]; then
    echo "❌ Database seeding failed. Please check the error above."
    exit 1
fi

echo "✅ Database seeding completed successfully"

# Clear application cache
echo "🧹 Clearing application cache..."
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan view:clear
echo "✅ Cache cleared successfully"

# Installation status is already set in .env file
echo "📝 Installation status is configured in .env file"

# Clear any Laravel console sessions
echo "🧹 Clearing Laravel console sessions..."
docker-compose exec app php artisan tinker --execute="exit;"
echo "✅ Console sessions cleared"

echo ""
echo "🎉 Default data initialization completed successfully!"
echo "================================================"
echo "📋 The following default data has been created:"
echo "   • Admin user (admin@example.com / 12345678)"
echo "   • Default roles and permissions"
echo "   • Application settings"
echo "   • Company information"
echo "   • Default language (English)"
echo "   • SMS and Email templates"
echo "   • Account groups and payment types"
echo "   • Default tax settings"
echo "   • Item categories and units"
echo "   • Default warehouse (Main)"
echo "   • State/region data"
echo "   • Version information"
echo ""
echo "🌐 You can now access the application at: http://localhost"
echo "🔑 Login with: admin@example.com / 12345678"
echo ""
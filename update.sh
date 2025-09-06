#!/bin/bash
set -e

echo "🚀 Updating Synkode-POS..."

# Step 0: Backup database
echo "📦 Creating database backup..."
docker compose exec app ./db-backup.sh || true

# Step 1: Stop containers
echo "🛑 Stopping containers..."
docker compose down

# Step 2: Pull latest code
echo "⬇️ Pulling latest code from GitHub..."
sudo chown -R $(whoami):$(whoami) storage bootstrap/cache || true
git reset --hard
git pull origin docker   # change to main if Docker moves there later

# Step 3: Rebuild app container
echo "🔨 Rebuilding app container..."
docker compose build app

# Step 4: Start containers
echo "🐳 Starting containers..."
docker compose up -d

# Step 5: Run migrations
echo "📂 Running migrations..."
docker compose exec app php artisan migrate --force

# Step 6: Run version seeder (if needed)
echo "🌱 Applying version seeder..."
docker compose exec app php artisan db:seed --class=VersionSeeder --force || true

# Step 7: Clear caches
echo "🧹 Clearing Laravel cache..."
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear

echo "✅ Update complete! POS is ready at http://localhost/login"

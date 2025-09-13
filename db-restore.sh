#!/bin/bash
set -e
echo "♻️ Restoring PostgreSQL Database..."

if [ -z "$1" ]; then
    echo "❌ Usage: ./db-restore.sh <backup-file.sql|backup-file.backup>"
    exit 1
fi

BACKUP_FILE=$1

if [ ! -f "$BACKUP_FILE" ]; then
    echo "❌ Backup file not found: $BACKUP_FILE"
    exit 1
fi

EXT="${BACKUP_FILE##*.}"

# Step 1: Drop and recreate the database (clean restore)
echo "🗑️ Dropping old database..."
docker compose exec -T db dropdb -U postgres laravel --if-exists
echo "🆕 Creating fresh database..."
docker compose exec -T db createdb -U postgres laravel

# Step 2: Restore based on extension
if [ "$EXT" = "sql" ]; then
    echo "📂 Restoring from SQL dump..."
    docker compose exec -T db psql -U postgres -d laravel < "$BACKUP_FILE"
elif [ "$EXT" = "backup" ]; then
    echo "📂 Restoring from custom .backup file..."
    docker compose exec -T db pg_restore -U postgres -d laravel --clean --if-exists "$BACKUP_FILE"
else
    echo "❌ Unsupported file type: $EXT"
    exit 1
fi

echo "✅ Restore complete!"

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

if [ "$EXT" = "sql" ]; then
    echo "📂 Restoring from SQL dump..."
    PGPASSWORD=postgres psql -h db -U postgres -d laravel < "$BACKUP_FILE"
elif [ "$EXT" = "backup" ]; then
    echo "📂 Restoring from custom .backup file..."
    PGPASSWORD=postgres pg_restore -h db -U postgres -d laravel --clean --if-exists "$BACKUP_FILE"
else
    echo "❌ Unsupported file type: $EXT"
    exit 1
fi

echo "✅ Restore complete!"

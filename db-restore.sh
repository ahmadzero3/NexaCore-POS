set -e
echo "♻️ Restoring PostgreSQL Database..."

if [ -z "$1" ]; then
    echo "❌ Usage: ./db-restore.sh <backup-file.sql>"
    exit 1
fi

BACKUP_FILE=$1

if [ ! -f "$BACKUP_FILE" ]; then
    echo "❌ Backup file not found: $BACKUP_FILE"
    exit 1
fi

echo "📂 Restoring from $BACKUP_FILE ..."
PGPASSWORD=postgres psql -h db -U postgres -d laravel < "$BACKUP_FILE"

echo "✅ Restore complete!"
EOF
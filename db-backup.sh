set -e
echo "🗄️ Running PostgreSQL Backup..."

BACKUP_DIR=storage/app/backups
mkdir -p $BACKUP_DIR
FILE="$BACKUP_DIR/backup_$(date +%Y-%m-%d_%H-%M-%S).sql"

PGPASSWORD=postgres pg_dump -h db -U postgres laravel > $FILE

echo "✅ Backup complete: $FILE"
EOF
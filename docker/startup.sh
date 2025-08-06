#!/bin/sh
set -e

echo "Starting Symfony application..."

# Warte auf Datenbank
echo "Waiting for database..."
while ! nc -z ${DATABASE_HOST:-db} ${DATABASE_PORT:-5432}; do
  sleep 1
done
echo "Database is ready!"

# Führe Symfony-Setup aus
echo "Running Symfony setup..."
php bin/console cache:clear --env=prod --no-debug
php bin/console cache:warmup --env=prod --no-debug

# Führe Migrationen aus
echo "Running database migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --env=prod

# Assets installieren (falls AssetMapper verwendet wird)
echo "Installing assets..."
php bin/console asset-map:compile --env=prod || echo "AssetMapper not available, skipping..."

# Setze finale Berechtigungen
echo "Setting final permissions..."
chown -R app:app var/
chmod -R 755 var/

# Erstelle Session-Verzeichnis mit korrekten Berechtigungen
echo "Creating session directory..."
mkdir -p var/sessions
chown -R app:app var/sessions
chmod -R 777 var/sessions

echo "Application is ready!"

# Starte Supervisor
exec /usr/bin/supervisord -c /etc/supervisord.conf
